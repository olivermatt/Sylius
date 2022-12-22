<?php

namespace Modena\PaymentGatewayPlugin\Payum\Lib;

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
    private $cancel_url;
    private $access_token;
    private $modena_redirect_url;
    private $loggingEnabled;
    private $logDir;

    public function __construct($api, $order, $billing_data, $customer, $return_url, $cancel_url)
    {
        $this->api = $api;
        $this->order = $order;
        $this->billing_data = $billing_data;
        $this->customer = $customer;
        $this->return_url = $return_url;
        $this->cancel_url = $cancel_url;
        
        if($this->api->options['loggingEnabled'] == "Yes") {
            $this->loggingEnabled = true;
        } else {
            $this->loggingEnabled = false;
        }

        $this->loggingEnabled = true;
        
        if($this->api->options['logDir'] == null || $this->api->options['logDir'] == '__DIR__') {
            $this->logDir = __DIR__;
        } else {
            $this->logDir = $this->api->options['logDir'];
        }

        //// LOGGING ////
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler($this->logDir.'/modena_payment.log', Logger::WARNING));  
        $log->warning('Logging enabled: ' . $this->loggingEnabled);
        $log->warning('Logging location: ' . $this->logDir .'/modena_payment.log');
        $log->warning('IT WORKS');
        /////


        $this->getAccessToken();
        $order_request_body = $this->buildOrderRequest();
        $this->sendOrder($order_request_body);

        header('Location: '. $this->modena_redirect_url);
        exit;
    }

 
    public function getAccessToken()
    {
        $client_id = $this->api->options['client_id'];
        $client_secret = $this->api->options['client_secret'];

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
            if($this->loggingEnabled) {
                $log = new Logger('Modena Log');
                $log->pushHandler(new StreamHandler($this->logDir.'/modena_payment.log', Logger::WARNING));      
                $log->warning('Unable to get access token. POST request failed.');
            }
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
            $customer['address'] =  $this->billing_data->getStreet() . " - ". $this->billing_data->getCity();
        } else if($product == 'HIRE-PURCHASE') {
            $request['maturityInMonths'] = 36;
            $customer['address'] = $this->billing_data->getStreet() . " - ". $this->billing_data->getCity();
        } else {
            $customer['firstName'] = $this->billing_data->getFirstName();
            $customer['lastName'] = $this->billing_data->getLastName();
            $request['selectedOption'] = $this->api->options['product'];
        }


        $customer['email'] = $this->customer->getEmail();
        $customer['phoneNumber'] = $this->billing_data->getPhoneNumber();


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
        $request['totalAmount'] = round($this->order->getTotal()/100,2);
        $request['currency'] = "EUR";
        $request['orderItems'] = $order_items;
        $request['customer'] = $customer;
        $request['timestamp'] = date("Y-m-d\TH:i:s.u\Z"); 
        $request['returnUrl'] = $this->return_url; 
        $request['cancelUrl'] = $this->cancel_url; 
        $request['callbackUrl'] = $this->return_url;

        return json_encode($request);
    }


    public function sendOrder($request_body)
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
            if($this->loggingEnabled) {
                $log = new Logger('Modena Log');
                $log->pushHandler(new StreamHandler($this->logDir.'/modena_payment.log', Logger::WARNING));       
                $log->warning('Unable to POST purchase order. Response '.$response->getStatusCode().', no redirect address.');     
            }             
            $this->modena_redirect_url =  $this->cancel_url;           
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