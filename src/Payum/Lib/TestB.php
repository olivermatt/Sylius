<?php

namespace Acme\SyliusExamplePlugin\Payum\Lib;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpClient\HttpClient;


use Payum\Core\Request\Generic;


class TestB extends Generic
{

    public function __construct($url)
    {
        $log = new Logger('Modena Log3');
        $log->pushHandler(new StreamHandler(__DIR__.'/lib_log.log', Logger::WARNING));        

        $token = $this->getAccessToken();
        $return_url = $this->sendslice($token);
        $log->warning('Inside TESTB, token: - ' . strlen($token));
        $log->warning('Inside TESTB, return url ' . $return_url);
        $log->warning('Inside TESTB return url strlen: ' . strlen($return_url));


        $this->diff();

        header('Location: https://google.com');
        exit;
    }

 
    private function diff()
    {
        $log = new Logger('Modena Log4');
        $log->pushHandler(new StreamHandler(__DIR__.'/lib_log.log', Logger::WARNING));        

        $client = HttpClient::create();

        $response = $client->request('POST', 'https://login-dev.modena.ee/oauth2/token', [
            'auth_basic' => ['4273d91f-e80f-410f-87cb-29a48a4b6e12', '44c77b8c-bc26-4bf3-bf88-f35fe6b189d1'],
            'body' => ['grant_type' => 'client_credentials', 'scope' => 'slicepayment']
        ]);
        
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        $log->warning('Curl HTTP resp diff: ' . $statusCode);
        $log->warning('Curl HTTP resp diff content: ' . $content);

    }

    
    private function getAccessToken() 
    {      
        $log = new Logger('Modena Log4');
        $log->pushHandler(new StreamHandler(__DIR__.'/lib_log.log', Logger::WARNING));        

        $user = '4273d91f-e80f-410f-87cb-29a48a4b6e12';
        $pass = '44c77b8c-bc26-4bf3-bf88-f35fe6b189d1';           
        $API_URL = 'https://webhook.site/5cadd40c-83aa-457f-8340-0216b99c6259'; ///'https://login-dev.modena.ee/oauth2/token';
        $data = "grant_type=client_credentials&scope=slicepayment";
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $API_URL);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $user.':'.$pass);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $raw_response = curl_exec($curl);

        $decoded_response = json_decode($raw_response);
       
        $info = curl_getinfo($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        $log->warning('Curl HTTP resp: ' . $info['http_code']);
        $log->warning('Curl HTTP resp: ' . $http_status);


        if(isset($decoded_response->access_token))
        {
            $accessToken = $decoded_response->access_token;
            curl_close($curl);
            
            return $accessToken;   
        } else{

            return "no_access_token";
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