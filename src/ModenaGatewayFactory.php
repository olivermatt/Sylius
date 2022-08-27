<?php

declare(strict_types=1);

namespace ModenaFin\SyliusModenaPlugin;

use ModenaFin\SyliusModenalugin\Bridge\OpenPayUBridgeInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class ModenaGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults(
            [
                'payum.factory_name' => 'payu',
                'payum.factory_title' => 'PayU',
            ]
        );

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => OpenPayUBridgeInterface::SANDBOX_ENVIRONMENT,
                'pos_id' => '',
                'signature_key' => '',
                'oauth_client_id' => '',
                'oauth_client_secret' => '',
            ];
            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = ['environment', 'pos_id', 'signature_key', 'oauth_client_id', 'oauth_client_secret'];

            $config['payum.api'] = static function (ArrayObject $config): array {
                $config->validateNotEmpty($config['payum.required_options']);

                return [
                    'environment' => $config['environment'],
                    'pos_id' => $config['pos_id'],
                    'signature_key' => $config['signature_key'],
                    'oauth_client_id' => $config['oauth_client_id'],
                    'oauth_client_secret' => $config['oauth_client_secret'],
                ];
            };
        }
    }
}
