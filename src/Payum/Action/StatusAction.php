<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class StatusAction implements ActionInterface
{

    public function execute($request): void
    {

        //// Logging ////
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));

        $trace = debug_backtrace();
        $class = $trace[1]['class'];


        $log->warning('StatusAction execute has been run, called by: ' . $class);
        ////

        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($model['statusModena'])) {

            $log->warning('StatusAction Model status new');

            $log->warning(get_object_vars($model));

            $request->markNew();

            return;
        } elseif ($model['statusModena'] == 'done') {

            $log->warning('StatusAction Model status done');

            $request->markCaptured();

            return;
        } elseif ($model['statusModena'] == 'cancelled') {
            $log->warning('StatusAction Model status cancelled');


            $request->markCanceled();
            return;
        } elseif ($model['statusModena'] == 'failed') {
            $log->warning('StatusAction Model status failed');

            $request->markFailed();
            return;
        } else {
            $log->warning('StatusAction Model status unknown');


            $request->markUnknown();
            return;
        }


        /*
        $details = $payment->getDetails();

        if (200 === $details['status']) {
            $request->markCaptured();

            return;
        }

        if (400 === $details['status']) {
            $request->markFailed();

            return;
        }
        */
        
        

    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getFirstModel() instanceof SyliusPaymentInterface
        ;
    }
}