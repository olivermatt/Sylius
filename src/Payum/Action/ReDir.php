<?php

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Reply\HttpRedirect;


class ReDir implements ActionInterface
{
    public function execute($request)
    {
        throw new HttpRedirect('http://example.com/auth');
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

        
    }




}