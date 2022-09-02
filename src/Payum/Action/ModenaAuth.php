<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Reply\HttpRedirect;


class ModenaAuth implements ActionInterface, ApiAwareInterface
{

    public function __construct()
    {

        echo '<script>alert("Modena AUth Called");</script>';

    }

    function execute($request): void
    {

        echo '<script>alert("ModenaAUTH exceute Called");</script>';


        try {
            /** @var \Payum\Core\Gateway $gateway */
            $gateway->addAction(new ReDir);
        
            $gateway->execute(new ReDir);
        } catch (HttpRedirect $reply) {
            header( 'Location: '.$reply->getUrl());
            exit;
        }





    }

}

