<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Model\ModelAwareInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\GetHumanStatus;
use Acme\SyliusExamplePlugin\Payum\Bridge\ModenaBridgeInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class StatusAction implements ActionInterface
{

    private $input;
    private $input2;

    /** @param OpenPayUBridgeInterface $openPayUBridge */
    ///     public function __construct(ModenaBridgeInterface $openPayUBridge)

    public function __construct($input2)
    {
        ///$this->input = $input;
        $this->input2 = $input2;

    }


    /** @var ModenaBridgeInterface */
    private $bridge;

    /** @param GetStatusInterface $request */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));
        //$log->warning('StatusAction testvar: ' .$this->input->mvars);
        $log->warning('StatusAction testvar2: ' .$this->input2);

        $log->warning('StatusAction model status: ' . $model['status']);

        $log->warning('StatusAction request type:' . gettype($request) . ", class instance: " . get_class($request));

        if($model==null)
        {
            $log->warning('StatusAction model is null');
        }
        else
        {
            $log->warning('StatusAction model is NOT null');
        }



        //// Logging ////
        $trace = debug_backtrace();
        $class = $trace[1]['class'];
        $function = $trace[1]['function'];

        $log->warning('StatusAction execute has been run, called by: ' . $class . ', func: '. $function);
        $log->warning('Status in Statusaction ' . $request->getValue());
       

        if($this->input2 == "DONE")
        {
            $request->markCaptured();
        }

        /*
        if (!isset($model['statusModena'])) {

            $log->warning('StatusAction Model status new, id: ');
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
        */


        /*
        $details = $payment->getDetails();

        if (200 === $details['status']) {
            $request->markCaptured();

            return;
        }

        if (400 === $details['status']) {
            $request->markFailed();

        }
        */
        
        return;
        

    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getFirstModel() instanceof SyliusPaymentInterface
        ;
    }
}