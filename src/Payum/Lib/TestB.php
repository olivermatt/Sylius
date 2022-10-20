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
        $decoded_response = json_decode($content);

        $log->warning('AccessToken HTTP resp status: ' . $statusCode);

        $this->sendslice($decoded_response->access_token);
    }

    
    private function sendslice($token)
    {
        $log = new Logger('Modena Log4');
        $log->pushHandler(new StreamHandler(__DIR__.'/lib_log.log', Logger::WARNING));        

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

        $client = HttpClient::create();

        $response = $client->request('POST', $api_url, [
            'auth_bearer' => $token,
            'body' => $content,
            'max_redirects' => 0,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        
        $redirect_url = $response->getInfo('redirect_url');
        $statusCode = $response->getStatusCode();

        $log->warning('Redir url: ' . $redirect_url);
        $log->warning('Create Order resp status: ' . $statusCode);
        
    }
}