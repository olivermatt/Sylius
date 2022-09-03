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

        $log->warning('StatusAction execute has been run');
        ////


        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($model['status'])) {
            echo '<script>alert("Model status new");</script>'; 

            $request->markNew();
            return;
        } elseif ($model['status'] == 'done') {
            echo '<script>alert("Model status done");</script>'; 

            $request->markCaptured();
            return;
        } elseif ($model['status'] == 'cancelled') {
            echo '<script>alert("Model status cancelled");</script>'; 


            $request->markCanceled();
            return;
        } elseif ($model['status'] == 'failed') {
            echo '<script>alert("Model status failed");</script>'; 

            $request->markFailed();
            return;
        } else {
            echo '<script>alert("Model status unkown");</script>'; 


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