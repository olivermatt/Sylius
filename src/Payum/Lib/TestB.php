<?php

namespace Acme\SyliusExamplePlugin\Payum\Lib;

use Monolog\Logger;
use Payum\Core\Request\Generic;


class TestB extends Generic
{

    public function __construct($url)
    {
        $log = new Logger('Modena Log2');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));        
        $log->warning('Inside TESTB 1');

        $token = $this->getAccessToken();
        $return_url = $this->sendslice($token);
        $log->warning('Inside TESTB 2 ' . $token);

        $log->warning('Inside TESTB 3 ' . $return_url);

        header('Location: '.$return_url.'?done=1');
        $log->warning('Inside TESTB 4');

        exit;
        $log->warning('Inside TESTB 5');

    }

    
    private function getAccessToken() 
    {      
        $user = '4273d91f-e80f-410f-87cb-29a48a4b6e12';
        $pass = '44c77b8c-bc26-4bf3-bf88-f35fe6b189d1';           
        $API_URL = 'https://login-dev.modena.ee/oauth2/token';
        $data = "grant_type=client_credentials&scope=slicepayment";
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $API_URL);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $user.':'.$pass);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $raw_response = curl_exec($curl);

        $decodedRespone = json_decode($raw_response);
       
        if(isset($decodedRespone->access_token))
        {
            $accessToken = $decodedRespone->access_token;
            curl_close($curl);
            return $accessToken;   
        }
    }


    private function sendslice($token)
    {

        $api_url = 'https://api-dev.modena.ee/modena/api/merchant/slice-payment-order';

        $content = '{
        "maturityInMonths": 3,
        "orderId": "123456",
        "totalAmount": 90,
        "currency": "EUR",
        "orderItems": [
            {
            "description": "Shoes",
            "amount": 10,
            "currency": "EUR",
            "quantity": 1
            }
        ],
        "customer": {
            "phoneNumber": "+372112233",
            "email": "jon.doe@localhost",
            "address": "Example street 12, Tallinn, 10333"
        },
        "timestamp": "2022-06-18T19:43:46.862Z",
        "returnUrl": "https://modena.ee/return-url",
        "cancelUrl": "https://modena.ee/cancel-url",
        "callbackUrl": "https://modena.ee/callback-url"
        }';


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            sprintf('authorization: Bearer %s', $token)
        ),

        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_TIMEOUT => 100,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLINFO_HEADER_OUT  => false,
        CURLOPT_POSTFIELDS => $content,
        ));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
                    
        return $info['redirect_url'];        
    }
}