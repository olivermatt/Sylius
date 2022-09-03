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
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;
 

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        //// Logging ////
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));

        $log->warning('ConvertPaymenetAction execute has been run');
        ////



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
