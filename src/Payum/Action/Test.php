<?php

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Reply\HttpRedirect;


class Test implements ActionInterface
{

    public function __construct()
    {
        echo '<script>alert("Modena TEST Called");</script>';
    }

    function execute() : void
    {
        echo '<script>alert("ModenaTEST Execute Called");</script>';
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




}