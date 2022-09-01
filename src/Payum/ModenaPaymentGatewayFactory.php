<?php

declare(strict_types=1);

namespace ModenaFin\SyliusExamplePlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class ModenaPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'sylius_payment',
            'payum.factory_title' => 'Modena Payment',
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new SyliusApi($config['api_key']);
        };

    }
}
