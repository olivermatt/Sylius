<?php

declare(strict_types=1);

namespace Modena\PaymentGatewayPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;


final class StatusAction implements ActionInterface
{

    /** @param GetStatusInterface $request */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        
        $model = ArrayObject::ensureArrayObject($request->getModel());

        if($model['status'] == "DONE")
        {
            $request->markCaptured();
            return;

        } elseif ($model['status'] == 'CANCEL') {
                  
            $request->markCanceled();
            return;
        }     
        
        return;       

    }

    public function supports($request)
    {
        return $request instanceof GetStatusInterface && $request->getModel() instanceof \ArrayAccess;
    }

}