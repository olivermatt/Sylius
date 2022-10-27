<?php

declare(strict_types=1);

namespace Modena\PaymentGatewayPlugin\Payum\Action;

use Modena\PaymentGatewayPlugin\Payum\Lib\ModenaPaymentManager;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\Capture;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
use Modena\PaymentGatewayPlugin\Payum\ModenaApi;


final class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    /** @var SyliusApi */
    private $api;

    //use ApiAwareTrait;
    use GatewayAwareTrait;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
    
        $model = ArrayObject::ensureArrayObject($request->getModel());
        $order = $request->getFirstModel()->getOrder();
        $customer = $order->getCustomer();        
        $billing_data = $order->getBillingAddress();
        
        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        if(isset($getHttpRequest->query['status'])) {
                if ($getHttpRequest->query['status']=='DONE') {
                   
                $model['status'] = 'DONE';
                return;          
            } elseif($getHttpRequest->query['status'] == 'CANCEL') {          
                $model['status'] = 'CANCEL';
                return;      
            } else {
                $model['status'] = 'CANCEL';
                return;      
            }  
        }

        ////////// Create a New Request /////////////
        $token = $request->getToken();
        $payment_done_return_url = $this->generateReturnURL($token, 'DONE');        
        $payment_cancelled_return_url = $this->generateReturnURL($token, 'CANCEL');        
    
        //// Execute Modena Payment 
        $this->gateway->execute(new ModenaPaymentManager($this->api, 
        $order, 
        $billing_data, 
        $customer, 
        $payment_done_return_url,
        $payment_cancelled_return_url
        ));
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
            return $token->getTargetUrl()."?status=DONE";
        } else {
            return $token->getTargetUrl()."?status=CANCEL";
        }
    }


}