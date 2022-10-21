<?php

namespace Acme\SyliusExamplePlugin\Payum\Lib;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpClient\HttpClient;


use Payum\Core\Request\Generic;


class ModenaPaymentManager extends Generic
{

    private $order;
    private $billing_data;
    private $customer;
    private $return_url;
    private $access_token;
    private $modena_redirect_url;

    public function __construct($order, $billing_data, $customer, $return_url)
    {

        $this->order = $order;
        $this->billing_data = $billing_data;
        $this->customer = $customer;
        $this->return_url = $return_url;


        $this->getAccessToken();
        $order_request_body = $this->buildOrderRequest();
        $this->sendslice($order_request_body);

        header('Location: '. $this->modena_redirect_url);
        exit;
    }

 
    public function getAccessToken()
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

        $this->access_token = $decoded_response->access_token;
    }

    
    public function buildOrderRequest()
    {
        $log = new Logger('Modena Log4');
        $log->pushHandler(new StreamHandler(__DIR__.'/lib_log.log', Logger::WARNING));        

        $customer = [];
        //$customer['firstName'] = $this->billingdata->getFirstName();
        //$customer['lastName'] = $this->billingdata->getLastName();
        $customer['phoneNumber'] = $this->billing_data->getPhoneNumber();
        $customer['email'] = $this->customer->getEmail();
        $customer['address'] = "Example street 12, Tallinn, 10333";

        $order_items = [];

        if ($items = $this->order->getItems()) {
            foreach ($items as $key => $item) {
                $orderItem = [];
                $orderItem['description'] = $item->getProductName();
                $orderItem['amount'] = $item->getUnitPrice() * $item->getQuantity();
                $orderItem['currency'] = 'EUR';
                $orderItem['quantity'] = $item->getQuantity();
                array_push($order_items, $orderItem);
            }
        }

        $request = [];
        $request['orderId'] = $this->order->getNumber();
        $request['maturityInMonths'] = 3;
        $request['orderItems'] = $order_items;
        $request['totalAmount'] = $this->order->getTotal();
        $request['currency'] = "EUR";
        $request['orderItems'] = $order_items;
        $request['customer'] = $customer;
        $request['timestamp'] = "2022-06-18T19:43:46.862Z"; 
        $request['returnUrl'] = $this->return_url; 
        $request['cancelUrl'] = "https://google.com"; 
        $request['callbackUrl'] = $this->return_url;

        $log->warning('Request: ' . json_encode($request));

        return json_encode($request);
    }



    public function sendslice($request_body)
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
            "amount": 90,
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
            'auth_bearer' => $this->access_token,
            'body' => $request_body,
            'max_redirects' => 0,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        
        $redirect_url = $response->getInfo('redirect_url');
        $statusCode = $response->getStatusCode();

        $r = json_encode($response->getInfo());

        $log->warning('order response info: ' . $r);

        /*
        foreach ($response->getInfo() as $method_name) {
           $log->warning('order response info: ' . $method_name);
        }
        */

        $this->modena_redirect_url = $redirect_url;
        $log->warning('Redir url: ' . $redirect_url);
        $log->warning('Create Order resp status: ' . $statusCode);
        
    }
}