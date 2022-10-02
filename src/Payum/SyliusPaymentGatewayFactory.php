<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Acme\SyliusExamplePlugin\Payum\Action\StatusAction;
use Acme\SyliusExamplePlugin\Payum\Bridge\ModenaBridgeInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


final class SyliusPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {

        $trace = debug_backtrace();
        $class = $trace[1]['class'];
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));        
        $log->warning('SyliusPaymentGatewayFactory populateconfig has been run, called by: ' . $class);

        $i = new ModenaVars();
        $i->mvars = "JULIUS CEASAR";

        $config->defaults([
            'payum.factory_name' => 'sylius_payment',
            'payum.factory_title' => 'Sylius Payment',
            'payum.action.capture' => new CaptureAction($i),
            'payum.action.status' => new StatusAction($i),
        ]);

        /// 

        $config['payum.api'] = function (ArrayObject $config) {
            return new SyliusApi($config['api_key']);
        };


    }
}

class ModenaVars
{
    public $mvars;
}