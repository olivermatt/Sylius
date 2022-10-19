<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;


use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\DoCapture;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;
use Payum\Core\Request\GetHumanStatus;
use Acme\SyliusExamplePlugin\Payum\Action\StatusAction;
use Acme\SyliusExamplePlugin\Payum\ModenaApi;
use Acme\SyliusExamplePlugin\Payum\Lib\ModenaPaymentManager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


final class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    private $client;

    /** @var SyliusApi */
    private $api;

    private $inputData;

    //use ApiAwareTrait;
    use GatewayAwareTrait;

 
    public function __construct($inputData = null)
    {
        if($inputData!=null)
        {
            $this->inputData = $inputData;
        }
        else
        {
            $this->inputData = "---";
        }
    }


    public function execute($request): void
    {

        RequestNotSupportedException::assertSupports($this, $request);
    
        $model = ArrayObject::ensureArrayObject($request->getModel());

        //// Logging ////
        $trace = debug_backtrace();
        $class = $trace[1]['class'];
        $function = $trace[1]['function'];
        $log = new Logger('Modena Log2');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));        
        $log->warning('v 1.2 CaptureAction execute has been run, called by: ' . $class . ', func: '. $function);
        $log->warning('CaptureAction request = ' . gettype($request) . " " . get_class($request));
        $log->warning('CaptureAction model API = ' . gettype($this->api) . " " . get_class($this->api));
        $log->warning('CaptureAction API var' . $this->api->testvar);
         
        $log->warning('CaptureAction API config' . $this->api->options['payum.factory_name']);
        $log->warning('CaptureAction API ADMIn config' . $this->api->options['environment']);

        $order = $request->getFirstModel()->getOrder();
        $customer = $order->getCustomer();

        $class_methods = get_class_methods($order);

        foreach ($class_methods as $method_name) {
            $log->warning('order method: ' . $method_name);
        }

        $log->warning('CaptureAction order  = ' . gettype($order) . " " . get_class($order));
        $log->warning('CaptureAction customer = ' . gettype($customer) . " " . get_class($customer));


        $payUdata['description'] = $order->getNumber();
        $payUdata['currencyCode'] = $order->getCurrencyCode();
        $payUdata['totalAmount'] = $order->getTotal();

        $log->warning('CaptureAction Order state' . $order->getState());

        $log->warning('CaptureAction Order total' . $order->getTotal());
        $log->warning('CaptureAction Order number' . $order->getNumber());
        $log->warning('CaptureAction Order currency' . $order->getCurrencyCode());
    
        $log->warning('CaptureAction Order Customer Id' . $customer->getId());

        $log->warning('CaptureAction Order Customer email' . $customer->getEmail());
        $log->warning('CaptureAction Order phone' . $customer->getPhoneNumber());

        $log->warning('CaptureAction Order firstName' . $customer->getFirstName());
        $log->warning('CaptureAction Order lastName' . $customer->getLastName());
        $log->warning('CaptureAction Order order count:' . count($this->getOrderItems($order)));

        $log->warning('CaptureAction Shipping cost: ' . $order->getShippingTotal());

        

        //// Receive Callback or Customer Return
        /// Get the GET request 
        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        /// Check the params of the requst, done means payment is done, proceed to make the order done
        if (isset($getHttpRequest->query['done']) && $getHttpRequest->query['done']) {
           
            /*
            if (!$this->requestHasValidMAC($getHttpRequest->request)) {       
                $model['status'] = 'failed';
                return;
            }
            */
            $token = $request->getToken(); 
            $log->warning('CaptureAction model token ' . $token->getHash());

            $log->warning('CaptureAction has marked the model as done');
           
            $model['status'] = 'DONE';
            return;

          
        }


        /////////////////////////////////////////////
        ////////// Create a New Request /////////////
        $token = $request->getToken();
        $return_url = $this->tokenresolver($token);
        
        $gwname = $token->getGatewayName();

        $log->warning('Return URL: ' . $return_url .'?done=1, token GW: ' . $gwname);
              
        ////header('Location: https://webhook.site/8c83605f-3347-4ad0-9b50-778dfc65dd89');

        
        $this->gateway->execute(new ModenaPaymentManager($return_url));




        /* -Authenticate
        if($this->gateway->addAction(new Test))
        {
            echo '<script>alert("Test added to gateway success");</script>';
            
        }
        else
        {
            echo '<script>alert("Test added to gateway fail");</script>';
        }
           
        $payment = $request->getModel();
        $order = $payment->getOrder();
        $customer = $order->getCustomer();

        */

        /*

        */

       /// exit();
        /*
        try {
            $response = $this->client->request('POST', 'https://modena.ee', [
                'body' => json_encode([
                    'price' => $payment->getAmount(),
                    'currency' => $payment->getCurrencyCode(),
                    'api_key' => $this->api->getApiKey(),
                ]),
            ]);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } finally {
            $payment->setDetails(['status' => $response->getStatusCode()]);
        }

        */
    }
    /*
    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }
    */
    public function supports($request)
    {
        return
            $request instanceof Capture && $request->getModel() instanceof \ArrayAccess
        ;
    }

    
    public function setApi($api): void
    {
        if (!$api instanceof ModenaApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . ModenaApi::class);
        }

        $this->api = $api;
    }

    public function tokenresolver(TokenInterface $token)
    {
        return $token->getTargetUrl();
    }

    private function getOrderItems($order): array
    {
        $itemsData = [];

        if ($items = $order->getItems()) {
            /** @var OrderItemInterface $item */
            foreach ($items as $key => $item) {
                $itemsData[$key] = [
                    'name' => $item->getProductName(),
                    'unitPrice' => $item->getUnitPrice(),
                    'quantity' => $item->getQuantity(),
                ];
            }
        }

        return $itemsData;
    }    



}