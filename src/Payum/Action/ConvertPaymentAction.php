<?php

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Convert;
use Payum\Core\Bridge\Spl\ArrayObject;
use BuyPlan\Payment\lib\BuyPlan\domain\Order;
use BuyPlan\Payment\lib\BuyPlan\domain\OrderRow;
use BuyPlan\Payment\lib\BuyPlan\domain\ClientInfo;
use Sylius\Component\Core\Model\PaymentInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;


class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface, LoggerAwareInterface
{
    use GatewayAwareTrait;
    use LoggerAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $this->logger->info("MODENA - Inside ConvertPaymentActions");

        echo '<script>alert("Converting Payment Done");</script>'; 


        $payment = $request->getSource();
        $order = $payment->getOrder();
        $customer = $order->getCustomer();


        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        // empty details are either not relevant, or will be supplied upon digital authentication
        $client = new ClientInfo(
            '',
            '',
            $customer->getEmail(),
            $customer->getPhoneNumber(),
            '',
            '',
            ''
        );

        $orderRows = array(new OrderRow(
            'Tellimuse Number: '.$order->getNumber(),
            '',
            round($order->getTotal() / 100, 2),
            '1'
        ));

        $orderSummary = new Order(
            $client,
            $orderRows,
            1
        );

        $details['order'] = json_encode($orderSummary);
        $details['amount'] = round($order->getTotal() / 100, 2);
        $details['currency'] = 'EUR';
        $details['reference'] = $order->getNumber();
        $details['message'] = $order->getNotes();

        $request->setResult((array) $details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Convert && $request->getSource() instanceof PaymentInterface && $request->getTo() == 'array';
    }
}
