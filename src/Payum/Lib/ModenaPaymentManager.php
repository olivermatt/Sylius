<?php

namespace Acme\SyliusExamplePlugin\Payum\Lib;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpClient\HttpClient;


use Payum\Core\Request\Generic;


class ModenaPaymentManager extends Generic
{
    private $api;
    private $order;
    private $billing_data;
    private $customer;
    private $return_url;
    private $access_token;
    private $modena_redirect_url;

    public function __construct($api, $order, $billing_data, $customer, $return_url)
    {
        $this->api = $api;
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
        $client_id = $this->api->options['client_id']; /// '4273d91f-e80f-410f-87cb-29a48a4b6e12'
        $client_secret = $this->api->options['client_secret']; //// 44c77b8c-bc26-4bf3-bf88-f35fe6b189d1

        if ($this->api->options['environment'] == 'DEV') {
            $devURL = '-dev';
        } else {
            $devURL = '';
        }
    
        switch($this->api->options['product']) {
            case "PAY-LATER":
            $scope = 'slicepayment';
            break;
            case "HIRE-PURCHASE":
            $scope = 'creditpayment';
            break;
            default:
            $scope = 'directpayment';
            break;
        }

        $client = HttpClient::create();

        $response = $client->request('POST', 'https://login'.$devURL.'.modena.ee/oauth2/token', [
            'auth_basic' => [$client_id, $client_secret],
            'body' => ['grant_type' => 'client_credentials', 'scope' => $scope]
        ]);
        
        $content = $response->getContent();
        $decoded_response = json_decode($content);

        if($response->getStatusCode() != 200) {
            $log = new Logger('Modena Log');
            $log->pushHandler(new StreamHandler(__DIR__.'/modena_payment.log', Logger::WARNING));      
            $log->warning('Unable to get access token. POST request failed.');
        }

        $this->access_token = $decoded_response->access_token;
    }

    
    public function buildOrderRequest()
    {

        $request = [];
        $customer = [];
        $order_items = [];
        $product = $this->api->options['product'];

        if($product == 'PAY-LATER') {
            $request['maturityInMonths'] = 3;
            $customer['phoneNumber'] = $this->billing_data->getPhoneNumber();
            $customer['address'] = "Example street 12, Tallinn, 10333";
        } else if($product == 'HIRE-PURCHASE') {
            $request['maturityInMonths'] = 36;
            $customer['phoneNumber'] = $this->billing_data->getPhoneNumber();
            $customer['address'] = "Example street 12, Tallinn, 10333";
        } else {
            $customer['firstName'] = $this->billing_data->getFirstName();
            $customer['lastName'] = $this->billing_data->getLastName();
            $request['selectedOption'] = $this->api->options['product'];
        }


        $customer['email'] = $this->customer->getEmail();


        if ($items = $this->order->getItems()) {
            foreach ($items as $key => $item) {
                $orderItem = [];
                $orderItem['description'] = $item->getProductName();
                $orderItem['amount'] = round(($item->getUnitPrice() * $item->getQuantity()/100),2);
                $orderItem['currency'] = 'EUR';
                $orderItem['quantity'] = $item->getQuantity();
                array_push($order_items, $orderItem);
            }
        }

        $request['orderId'] = $this->order->getNumber();
        $request['orderItems'] = $order_items;
        $request['totalAmount'] = round($this->order->getTotal()/100,2);
        $request['currency'] = "EUR";
        $request['orderItems'] = $order_items;
        $request['customer'] = $customer;
        $request['timestamp'] = "2022-06-18T19:43:46.862Z"; 
        $request['returnUrl'] = $this->return_url; 
        $request['cancelUrl'] = "https://google.com"; 
        $request['callbackUrl'] = $this->return_url;

        return json_encode($request);
    }


    public function sendslice($request_body)
    {
        $client = HttpClient::create();

        $response = $client->request('POST', $this->getAPIURL(), [
            'auth_bearer' => $this->access_token,
            'body' => $request_body,
            'max_redirects' => 0,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        
        if($response->getStatusCode() != 302) {
            $log = new Logger('Modena Log');
            $log->pushHandler(new StreamHandler(__DIR__.'/modena_payment.log', Logger::WARNING));       
            $log->warning('Unable to POST purchase order. Response not 302, no redirect address.'); 
        } else {
            $redirect_url = $response->getInfo('redirect_url');
            $this->modena_redirect_url = $redirect_url;
        }

        return;        
    }


    public function getAPIURL()
    {
        $product = $this->api->options['product'];

        if ($this->api->options['environment'] == 'DEV') {
            $devURL = '-dev';
        } else {
            $devURL = '';
        }

        if ($product == 'PAY-LATER') {
            return 'https://api' . $devURL . '.modena.ee/modena/api/merchant/slice-payment-order';
        } elseif ($product == 'HIRE-PURCHASE') {
            return 'https://api' . $devURL . '.modena.ee/modena/api/merchant/credit-payment-order';
        } else {
            return 'https://api' . $devURL . '.modena.ee/direct/api/partner-direct-payments/payment-order';
        }
    }
}