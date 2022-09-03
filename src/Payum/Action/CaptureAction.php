<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Acme\SyliusExamplePlugin\Payum\SyliusApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\DoCapture;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    /** @var Client */
    private $client;
    /** @var SyliusApi */
    private $api;

    use GatewayAwareTrait;

    public function __construct(Client $client)
    {
        $this->client = $client;

        // Create the logger
        $logger = new Logger('my_logger');
        // Now add some handlers
        $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
        $logger->pushHandler(new FirePHPHandler());

        // You can now use your logger
        $logger->info('My logger is now ready');

    }


    public function execute($request): void
    {

        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());




        //// Receive Callback or Customer Return

        /// Get the GET request 
        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        /// Check the params of the requst
        if (isset($getHttpRequest->query['done']) && $getHttpRequest->query['done']) {

            echo '<script>alert("Customer Return Done");</script>'; 
            
            /*
            if (!$this->requestHasValidMAC($getHttpRequest->request)) {
                                
                $model['status'] = 'failed';
                
                return;
            }
            */

            $model['status'] = 'done';
            return;
        }


        ////////////////////////////////////////


        //// Create a New Request /////////////
         $url = $this->tokenresolver($request->getToken());
         echo '<script>alert("Redirect URL: '.$url.'");</script>'; 

        $this->gateway->execute(new TestB($url));

        //// Authenticate
        if($this->gateway->addAction(new Test))
        {
            echo '<script>alert("Test added to gateway success");</script>';
            
        }
        else
        {
            echo '<script>alert("Test added to gateway fail");</script>';
        }
           
        $payment = $request->getModel();
        $order = $payment->getOrder();
        $customer = $order->getCustomer();


        ////$details['order'] = json_encode($orderSummary);
        $details['amount'] = round($order->getTotal() / 100, 2);
        $details['currency'] = 'EUR';
        $details['reference'] = $order->getNumber();
        $details['message'] = $order->getNotes();

        $clientEmail = $customer->getEmail();
        $clientPhone = $customer->getPhoneNumber(); 

        echo $clientEmail;
        echo " --- ";
        echo $clientPhone;
        echo "tellimuse nr: " . $order->getNumber();
        echo "tellimuse summa: " . round($order->getTotal() / 100, 2);


        /*

        $itemsData = [];


        if ($items = $order->getItems()) {

            foreach ($items as $key => $item) {
                $itemsData[$key] = [
                    'name' => $item->getProductName(),
                    'unitPrice' => $item->getUnitPrice(),
                    'quantity' => $item->getQuantity(),
                ];
            }
        }

        var_dump($itemsData);

        */

       /// exit();
        /*
        try {
            $response = $this->client->request('POST', 'https://modena.ee', [
                'body' => json_encode([
                    'price' => $payment->getAmount(),
                    'currency' => $payment->getCurrencyCode(),
                    'api_key' => $this->api->getApiKey(),
                ]),
            ]);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } finally {
            $payment->setDetails(['status' => $response->getStatusCode()]);
        }

        */
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }

    
    public function setApi($api): void
    {
        if (!$api instanceof SyliusApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusApi::class);
        }

        $this->api = $api;
    }

    public function tokenresolver(TokenInterface $token)
    {
        return $token->getTargetUrl();
    }



}