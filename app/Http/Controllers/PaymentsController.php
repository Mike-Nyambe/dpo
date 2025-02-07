<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;


class PaymentsController extends Controller
{
    private static $endpoint_url = "https://secure.3gdirectpay.com/API/v6/";
    private static $CompanyToken;
    private static $serviceType;

    public static $ref;

    public function __construct()
    {
        // Load sensitive data from environment variables for security
        self::$CompanyToken = env('DPO_COMPANY_TOKEN', '');
        self::$serviceType = env('DPO_SERVICE_TYPE', '');
    }

    // Create a reusable method for cURL requests
    private static function sendRequest($xmlData)
    {
        $ch = curl_init();

        if (!$ch) {
            throw new Exception("Couldn't initialize a cURL handle");
        }

        curl_setopt($ch, CURLOPT_URL, self::$endpoint_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);

        if ($result === false) {
            $errorMsg = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL execution failed: " . $errorMsg);
        }

        curl_close($ch);

        return $result;
    }

    public static function createChargeToken($ServiceDescription, $PaymentAmount, $PaymentCurrency)
    {
        $ServiceDate = date('Y-m-d H:i:s');
        $xmlData = "<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G>
        <CompanyToken>" . self::$CompanyToken . "</CompanyToken>
        <Request>createToken</Request>
        <Transaction>
            <PaymentAmount>" . $PaymentAmount . "</PaymentAmount>
            <PaymentCurrency>" . $PaymentCurrency . "</PaymentCurrency>
            <CompanyRef>" . uniqid() . "</CompanyRef>
            <CompanyRefUnique>0</CompanyRefUnique>
            <PTL>5</PTL>
        </Transaction>
        <Services>
            <Service>
                <ServiceType>" . self::$serviceType . "</ServiceType>
                <ServiceDescription>" . $ServiceDescription . "</ServiceDescription>
                <ServiceDate>" . $ServiceDate . "</ServiceDate>
            </Service>
        </Services>
    </API3G>";

        try {
            $result = self::sendRequest($xmlData);
            $response = simplexml_load_string($result);

            if ($response === false) {
                throw new Exception("Failed to parse XML response");
            }

            // Check if the response contains an error
            if (isset($response->Result) && $response->Result != '000') {
                throw new Exception($response->ResultExplanation);
            }

            return $response; // Return the SimpleXMLElement object
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Error in createChargeToken: " . $e->getMessage());
            return ["error" => $e->getMessage()]; // Return an array with the error message
        }
    }

    // Charge with credit card
    public function chargeCreditCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string',
            'expiry_date' => 'required|string',
            'cvv' => 'required|string',
            'cardholder_name' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        try {
            // Generate transaction token
            $transToken = self::createChargeToken(
                "Payment for service",
                $request->amount,
                $request->currency
            );

            // Check if there was an error
            if (isset($transToken['error'])) {
                throw new Exception($transToken['error']);
            }

            // Ensure $transToken is a SimpleXMLElement object
            if ($transToken->Result == '000') {
                // Prepare XML data for credit card charging
                $xmlData = '
    <?xml version="1.0" encoding="utf-8"?>
    <API3G>
        <CompanyToken>' . self::$CompanyToken . '</CompanyToken>
        <Request>chargeTokenCreditCard</Request>
        <TransactionToken>' . $transToken->TransToken . '</TransactionToken>
        <CreditCardNumber>' . $request->card_number . '</CreditCardNumber>
        <CreditCardExpiry>' . $request->expiry_date . '</CreditCardExpiry>
        <CreditCardCVV>' . $request->cvv . '</CreditCardCVV>
        <CardHolderName>' . $request->cardholder_name . '</CardHolderName>
    </API3G>';

                // Send the request to the payment gateway
                $result = self::sendRequest($xmlData);
                $response = simplexml_load_string($result);

                if ($response === false) {
                    throw new Exception("Failed to parse XML response");
                }

                if ($response->Result == '000') {
                    return redirect()->route('payment.success')->with('transaction_id', $transToken->TransToken);
                } else {
                    return redirect()->route('payment.failure')->with('error', $response->ResultExplanation);
                }
            } else {
                throw new Exception("Error generating transaction token: " . $transToken->ResultExplanation);
            }
        } catch (Exception $e) {
            return redirect()->route('payment.failure')->with('error', $e->getMessage());
        }
    }


    // Charge with mobile money
    public function chargeMobileMoney(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'mno' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'country' => 'required|string',
        ]);

        try {
            $transToken = self::createChargeToken(
                "Payment for service",
                $request->amount,
                $request->currency
            );

            // Check if there was an error
            if (isset($transToken['error'])) {
                throw new Exception($transToken['error']);
            }

            // Ensure $transToken is a SimpleXMLElement object
            if ($transToken->Result == '000') {
                $xmlData = '
    <?xml version="1.0" encoding="UTF-8"?>
    <API3G>
        <CompanyToken>' . self::$CompanyToken . '</CompanyToken>
        <Request>ChargeTokenMobile</Request>
        <TransactionToken>' . $transToken->TransToken . '</TransactionToken>
        <PhoneNumber>' . $request->phone . '</PhoneNumber>
        <MNO>' . $request->mno . '</MNO>
        <MNOcountry>' . $request->country . '</MNOcountry>
    </API3G>';

                $result = self::sendRequest($xmlData);
                $response = simplexml_load_string($result);

                if ($response->Result == '000') {
                    return redirect()->route('payment.success')->with('transaction_id', $transToken->TransToken);
                } else {
                    return redirect()->route('payment.failure')->with('error', $response->ResultExplanation);
                }
            } else {
                throw new Exception("Error generating transaction token: " . $transToken->ResultExplanation);
            }
        } catch (Exception $e) {
            return redirect()->route('payment.failure')->with('error', $e->getMessage());
        }
    }

    // Verify transaction
    public function verifyTransaction($token)
    {
        $xmlData = '
<?xml version="1.0" encoding="UTF-8"?>
<API3G>
    <CompanyToken>' . self::$CompanyToken . '</CompanyToken>
    <Request>verifyToken</Request>
    <TransactionToken>' . $token . '</TransactionToken>
</API3G>';

        try {
            $result = self::sendRequest($xmlData);
            $response = simplexml_load_string($result);

            if ($response === false) {
                throw new Exception("Failed to parse XML response");
            }

            if ($response->Result == '000') {
                return "success";
            } elseif ($response->Result == '904' || $response->Result == '903') {
                return "rejected";
            } else {
                return "pending";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return "error";
        }
    }

    // Payment success page
    public function paymentSuccess()
    {
        return view('success');
    }

    // Payment failure page
    public function paymentFailure()
    {
        return view('failure');
    }
}
