<?php

namespace Acme\SyliusExamplePlugin\Payum\Lib;

use Payum\Core\Request\Generic;
use Acme\SyliusExamplePlugin\Payum\Lib\SendAuthRequest;
use Payum\Core\Action\ActionInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;

class ModenaPaymentManager implements ActionInterface, GatewayAwareInterface
{

/// extends Generic  

    private $return_url;
    private $api;

    use GatewayAwareTrait;

    public function __construct()
    {
        $this->return_url = "abc";
        $this->startProcess();     
    }

    public function execute($request)
    {
        //do its jobs

        // delegate some job to bar action.
    }

    public function supports($request)
    {

    }

    
    public function setApi($api): void
    {
        if (!$api instanceof ModenaApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . ModenaApi::class);
        }

        $this->api = $api;
    }


    private function startProcess()
    {                      
        header('Location: '.$this->return_url.'?done=1');

        /// Execute auth request
        /*
        $authenticationAPI = new SendAuthRequest();
        $Token = $authenticationAPI->execute();

        if(!$Token)
        {
            echo "T-error. Tehniline viga, palun liikuge tagasi ja proovige uuesti.";
            exit;
        }

        //// Build order request
        $OrderRequest = new OrderRequestBuilder($Token,$POSTInput);

        /// Execute order request
        $OrderAPI = new SendOrderRequest($OrderRequest);
        $this->redirectUrl = $OrderAPI->execute();
        */

    }

    
}