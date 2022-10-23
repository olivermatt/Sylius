<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Acme\SyliusExamplePlugin\Payum\Lib\ModenaPaymentManager;
use Payum\Core\ApiAwareInterface;
///use Payum\Core\ApiAwareTrait;
///use GuzzleHttp\Client;
///use GuzzleHttp\Exception\RequestException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
///use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
///use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\DoCapture;
///use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
///use Payum\Core\Request\RenderTemplate;
///use Payum\Core\Request\GetHumanStatus;
//use Acme\SyliusExamplePlugin\Payum\Action\StatusAction;
use Acme\SyliusExamplePlugin\Payum\ModenaApi;
//use Acme\SyliusExamplePlugin\Payum\Lib\ModenaPaymentManager;
use Acme\SyliusExamplePlugin\Payum\Lib\TestB;

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
    
        ///$model = ArrayObject::ensureArrayObject($request->getModel());

        //// Logging ////
        $log = new Logger('Modena Log2');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));        

        $order = $request->getFirstModel()->getOrder();
        $customer = $order->getCustomer();        
        $billing_data = $order->getBillingAddress();
        
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
            $model['status'] = 'DONE';
            return;          
        } elseif($getHttpRequest->query['status'] == 'CANCEL') {
            $model['status'] = 'CANCEL';
            return;      
        }

        ////////// Create a New Request /////////////
        $token = $request->getToken();
        $payment_done_return_url = $this->generateReturnURL($token, 'DONE');        
        $payment_cancelled_return_url = $this->generateReturnURL($token, 'CANCEL');        
    
        $log->warning('Return URL .' . $payment_done_return_url); 
        $log->warning('Return Cancel URL .' . $payment_cancelled_return_url); 

        //// Execute Modena Payment 
        $this->gateway->execute(new ModenaPaymentManager($this->api, $order, $billing_data, $customer, $payment_done_return_url));
        ////
    }

    
    public function supports($request)
    {
        return $request instanceof Capture && $request->getModel() instanceof \ArrayAccess;
    }

    
    public function setApi($api): void
    {
        if (!$api instanceof ModenaApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . ModenaApi::class);
        }
        $this->api = $api;
    }


    public function generateReturnURL(TokenInterface $token, $status)
    {
        if($status == 'DONE') {
            return $token->getTargetUrl()."&status=done";
        } else {
            return $token->getTargetUrl()."&status=cancel";
        }
    }


}