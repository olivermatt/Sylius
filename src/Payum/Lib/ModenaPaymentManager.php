<?php

namespace Acme\SyliusExamplePlugin\Payum\Lib;

use Payum\Core\Request\Generic;
use Acme\SyliusExamplePlugin\Payum\Lib\SendAuthRequest;


class ModenaPaymentManager extends Generic
{

        private $return_url;


    public function __construct($return_url)
    {
        $this->return_url = $return_url;
        $this->startProcess();     
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