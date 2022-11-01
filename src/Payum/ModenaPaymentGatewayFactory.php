<?php

declare(strict_types=1);

namespace Modena\PaymentGatewayPlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Modena\PaymentGatewayPlugin\Payum\Action\StatusAction;
use Modena\PaymentGatewayPlugin\Payum\Action\CaptureAction;
use Modena\PaymentGatewayPlugin\Payum\ModenaApi;


final class ModenaPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'modena_payment',
            'payum.factory_title' => 'Modena Payment',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
        ]);

         $config['payum.api'] = function (ArrayObject $config) {

            return new ModenaApi((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
        };

    }
}

