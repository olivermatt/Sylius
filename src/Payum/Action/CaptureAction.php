<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Acme\SyliusExamplePlugin\Payum\SyliusApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
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
use Acme\SyliusExamplePlugin\Payum\Bridge\ModenaBridgeInterface;
use Acme\SyliusExamplePlugin\Payum\Action\StatusAction;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;


final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    /** @var Client */
    private $client;
    /** @var SyliusApi */
    private $api;

    private $openPayUBridge;
    private $classmethods;

    use GatewayAwareTrait;

    ///function __construct(ModenaBridgeInterface $openPayUBridge);

 
    public function __construct($client)
    {
        $this->client = $client;
        ///$classmethods = get_class_methods($client);

        ///$this->openPayUBridge = $openPayUBridge;
        ///$this->openPayUBridge->testvar = "OLIVER TESTING";
    }


    public function execute($request): void
    {

        RequestNotSupportedException::assertSupports($this, $request);
    
        $model = ArrayObject::ensureArrayObject($request->getModel());

        //// Logging ////
        $trace = debug_backtrace();
        $class = $trace[1]['class'];
        $function = $trace[1]['function'];
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));        
        $log->warning('v 1.2 CaptureAction execute has been run, called by: ' . $class . ', func: '. $function);
        $log->warning('CaptureAction request = ' . gettype($request) . " " . get_class($request));
        ////
        

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
            $log->warning('CaptureAction has marked the model as done');
            $this->client->mvars = "DONE";
            $model['status'] = 'DONE';
            return;

            $request->setModel($model);

            $token = $request->getToken(); 

            ///$this->gateway->execute($status = new GetHumanStatus($token));
            
            $log->warning('CaptureAction has marked the model as done');



            ///$status->markCaptured();

            ///$log->warning('CaptureAction status value ' . $status->getValue());


            return;
        }


        /////////////////////////////////////////////
        ////////// Create a New Request /////////////
        $token = $request->getToken();
        $url = $this->tokenresolver($token);
        
        $gwname = $token->getGatewayName();

        $log->warning('Return URL: ' . $url .'?done=1, token GW: ' . $gwname);
              
        ////header('Location: https://webhook.site/8c83605f-3347-4ad0-9b50-778dfc65dd89');

        
        $this->gateway->execute(new TestB($url));




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


        ////$details['order'] = json_encode($orderSummary);
        $details['amount'] = round($order->getTotal() / 100, 2);
        $details['currency'] = 'EUR';
        $details['reference'] = $order->getNumber();
        $details['message'] = $order->getNotes();

        $clientEmail = $customer->getEmail();
        $clientPhone = $customer->getPhoneNumber(); 

        */

        /*

        $itemsData = [];


        if ($items = $order->getItems()) {

            foreach ($items as $key => $item) {
                $itemsData[$key] = [
                    'name' => $item->getProductName(),
                    'unitPrice' => $item->getUnitPrice(),
                    'quantity' => $item->getQuantity(),
                ];
            }
        }

        var_dump($itemsData);

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

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }

    
    public function setApi($api): void
    {
        if (!$api instanceof SyliusApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusApi::class);
        }

        $this->api = $api;
    }

    public function tokenresolver(TokenInterface $token)
    {
        return $token->getTargetUrl();
    }

    



}