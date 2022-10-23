<?php

/*
declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


final class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /*
     * {@inheritdoc}
     *
     * @param Convert $request
     */

     /*

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        //// Logging ////
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));    
        $log->warning('ConvertPayment execute has been run');
        ////

        $payment = $request->getSource();
        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details['totalAmount'] = $payment->getTotalAmount();
        $details['currencyCode'] = $payment->getCurrencyCode();
        $details['extOrderId'] = uniqid((string) $payment->getNumber(), true);
        $details['description'] = $payment->getDescription();
        $details['client_email'] = $payment->getClientEmail();
        $details['client_id'] = $payment->getClientId();
        $details['customerIp'] = $this->getClientIp();
        $details['status'] = OpenPayUBridge::NEW_API_STATUS;

        $request->setResult((array) $details);
    }

    public function supports($request): bool
    {
        return $request instanceof Convert
               && $request->getSource() instanceof PaymentInterface
               && 'array' === $request->getTo();
    }

    private function getClientIp(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
}

*/