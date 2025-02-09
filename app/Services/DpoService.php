<?php

namespace App\Services;

class DpoService
{
    private static $endpoint_url = "https://secure.3gdirectpay.com/API/v6/";

    private static $CompanyToken = "83DBBABA-87CF-4901-B1CB-DBEBD5BBB850";

    private static $serviceType = "78752";

    public static $ref;

    function __construct($ref)
    {
        DpoService::$ref = $ref;
    }


    // Create a DPO token
    public static function CreateChargeToken($RedirectURL, $BackURL, $ServiceDescription, $PaymentAmount, $PaymentCurrency)
    {
        $ServiceDate = date('Y-m-d H:i:s');
        $endpoint = DpoService::$endpoint_url;
        $xmlData = "<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>" . DpoService::$CompanyToken . "</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>" . $PaymentAmount . "</PaymentAmount><PaymentCurrency>" . $PaymentCurrency . "</PaymentCurrency><CompanyRef>" . DpoService::$ref . "</CompanyRef><RedirectURL>" . $RedirectURL . "</RedirectURL><BackURL>" . $BackURL . "</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL></Transaction><Services><Service><ServiceType>" . DpoService::$serviceType . "</ServiceType><ServiceDescription>" . $ServiceDescription . "</ServiceDescription><ServiceDate>" . $ServiceDate . "</ServiceDate></Service></Services></API3G>";

        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);

        curl_close($ch);

        // Parse the XML response using SimpleXML
        $response = simplexml_load_string($result);

        // Wrap in an object instead of an array
        $responseObject = new \stdClass();
        $responseObject->message = $result;
        $responseObject->response = $response;

        return $responseObject;
    }



    //Charge mobile money

    public static function chargeTokenMobileMoney($mno, $phone, $amount, $country, $PaymentCurrency)
    {
        // Generate a transaction token
        $transToken = self::CreateChargeToken("https://webhook.site/54e2e771-bbc5-4818-bc1a-b0920dd1d797", "https://webhook.site/54e2e771-bbc5-4818-bc1a-b0920dd1d797", "Pay product", $amount, $PaymentCurrency);

        if ($transToken->response->Result == 000) {
            $ServiceDate = date('Y-m-d H:i:s');
            $endpoint = DpoService::$endpoint_url;
            $xmlData = '<?xml version="1.0" encoding="UTF-8"?> <API3G> <CompanyToken>' . DpoService::$CompanyToken . '</CompanyToken> <Request>ChargeTokenMobile</Request> <TransactionToken>' . $transToken->response->TransToken . '</TransactionToken> <PhoneNumber>' . $phone . '</PhoneNumber> <MNO>' . $mno . '</MNO> <MNOcountry>' . $country . '</MNOcountry> </API3G>';

            $ch = curl_init();

            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }

            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

            $result = curl_exec($ch);

            // Check if the curl_exec operation was successful
            if ($result === false) {
                die("cURL execution failed: " . curl_error($ch));
            }

            // Check if the transaction is successful based on the response
            $isSUccess = false;
            if (strpos($result, '130') !== false) {
                $isSUccess = true;
            }

            // Return the result along with the token and success status
            return ["result" => $result, "token" => $transToken->response->TransToken, 'isSuccess' => $isSUccess];
        } else {
            // Handle failure to generate token
            echo $transToken->message;
            exit();
        }
    }



    //Verify transaction
    //Charge mobile money

    public  static function verifyTrans($token)
    {


        $ServiceDate = date('Y-m-d H:i:s');
        $endpoint = self::$endpoint_url;
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?> <API3G> <CompanyToken>' . DpoService::$CompanyToken . '</CompanyToken> <Request>verifyToken</Request> <TransactionToken>' . $token . '</TransactionToken>  </API3G>';

        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);
        //echo $result;




        // Check if the curl_exec operation was successful
        if ($result === false) {
            die("cURL execution failed: " . curl_error($ch));
        }



        // Parse the XML response using SimpleXML


        $response = simplexml_load_string($result);

        $tran_status = "pending";



        if ($response->Result == 000) {
            $tran_status = "success";
        } elseif ($response->Result == 904 || $response->Result == 903) {
            $tran_status = "rejected";
        } else {
            $tran_status = "pending";
        }


        return ["tran_status" => $tran_status];
    }
}
