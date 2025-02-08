<?php

class dpoPay
{
    private $endpoint_url;
    private $companyToken;
    private $serviceType;
    private $ref;

    public function __construct($ref)
    {
        $this->endpoint_url = getenv('DPO_ENDPOINT_URL') ?: "https://secure.3gdirectpay.com/API/v6/";
        $this->companyToken = getenv('DPO_COMPANY_TOKEN') ?: "your_company_token";
        $this->serviceType = getenv('DPO_SERVICE_TYPE') ?: "service_type_code";
        $this->ref = $ref;
    }

    private function sendRequest($xmlData)
    {
        $ch = curl_init();

        if (!$ch) {
            throw new Exception("Couldn't initialize a cURL handle");
        }

        curl_setopt($ch, CURLOPT_URL, $this->endpoint_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
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

    public function createChargeToken($redirectURL, $backURL, $serviceDescription, $paymentAmount, $paymentCurrency)
    {
        $serviceDate = date('Y-m-d H:i:s');
        $xmlData = '<?xml version="1.0" encoding="utf-8"?>
<API3G>
    <CompanyToken>' . $this->companyToken . '</CompanyToken>
    <Request>createToken</Request>
    <Transaction>
        <PaymentAmount>' . $paymentAmount . '</PaymentAmount>
        <PaymentCurrency>' . $paymentCurrency . '</PaymentCurrency>
        <CompanyRef>' . $this->ref . '</CompanyRef>
        <RedirectURL>' . $redirectURL . '</RedirectURL>
        <BackURL>' . $backURL . '</BackURL>
        <CompanyRefUnique>0</CompanyRefUnique>
        <PTL>5</PTL>
    </Transaction>
    <Services>
        <Service>
            <ServiceType>' . $this->serviceType . '</ServiceType>
            <ServiceDescription>' . $serviceDescription . '</ServiceDescription>
            <ServiceDate>' . $serviceDate . '</ServiceDate>
        </Service>
    </Services>
</API3G>';

        $result = $this->sendRequest($xmlData);
        $response = simplexml_load_string($result);

        if ($response === false) {
            throw new Exception("Failed to parse XML response");
        }

        return $response;
    }

    public function chargeTokenCreditCard($creditCardNumber, $creditCardExpiry, $creditCardCVV, $cardHolderName, $amount, $paymentCurrency, $redirectURL, $backURL)
    {
        $transToken = $this->createChargeToken($redirectURL, $backURL, "Pay product", $amount, $paymentCurrency);

        if ($transToken->Result == '000') {
            $xmlData = '<?xml version="1.0" encoding="utf-8"?>
<API3G>
    <CompanyToken>' . $this->companyToken . '</CompanyToken>
    <Request>chargeTokenCreditCard</Request>
    <TransactionToken>' . $transToken->TransToken . '</TransactionToken>
    <CreditCardNumber>' . $creditCardNumber . '</CreditCardNumber>
    <CreditCardExpiry>' . $creditCardExpiry . '</CreditCardExpiry>
    <CreditCardCVV>' . $creditCardCVV . '</CreditCardCVV>
    <CardHolderName>' . $cardHolderName . '</CardHolderName>
</API3G>';

            $result = $this->sendRequest($xmlData);
            $response = simplexml_load_string($result);

            if ($response === false) {
                throw new Exception("Failed to parse XML response");
            }

            if ($response->Code != '000') {
                throw new Exception("Error: " . $response->ResultExplanation);
            }

            return $response;
        } else {
            throw new Exception("Error creating transaction token: " . $transToken->ResultExplanation);
        }
    }

    public function chargeTokenMobileMoney($mno, $phone, $amount, $country, $paymentCurrency, $redirectURL, $backURL)
    {
        $transToken = $this->createChargeToken($redirectURL, $backURL, "Pay product", $amount, $paymentCurrency);

        if ($transToken->Result == '000') {
            $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<API3G>
    <CompanyToken>' . $this->companyToken . '</CompanyToken>
    <Request>ChargeTokenMobile</Request>
    <TransactionToken>' . $transToken->TransToken . '</TransactionToken>
    <PhoneNumber>' . $phone . '</PhoneNumber>
    <MNO>' . $mno . '</MNO>
    <MNOcountry>' . $country . '</MNOcountry>
</API3G>';

            $result = $this->sendRequest($xmlData);
            $response = simplexml_load_string($result);

            if ($response === false) {
                throw new Exception("Failed to parse XML response");
            }

            return ["result" => $result, "token" => $transToken->TransToken, 'isSuccess' => ($response->Result == '000')];
        } else {
            throw new Exception("Error creating transaction token: " . $transToken->ResultExplanation);
        }
    }

    public function verifyTrans($token)
    {
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<API3G>
    <CompanyToken>' . $this->companyToken . '</CompanyToken>
    <Request>verifyToken</Request>
    <TransactionToken>' . $token . '</TransactionToken>
</API3G>';

        $result = $this->sendRequest($xmlData);
        $response = simplexml_load_string($result);

        if ($response === false) {
            throw new Exception("Failed to parse XML response");
        }

        $tran_status = "pending";

        if ($response->Result == '000') {
            $tran_status = "success";
        } elseif ($response->Result == '904' || $response->Result == '903') {
            $tran_status = "rejected";
        }

        return ["tran_status" => $tran_status];
    }
}
