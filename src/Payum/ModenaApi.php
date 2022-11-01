<?php

declare(strict_types=1);

namespace Modena\PaymentGatewayPlugin\Payum;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;

final class ModenaApi
{

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    public $options = [];

    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /*
    protected function doRequest($method, array $fields)
    {
        /*
        $log = new Logger('Modena Log');
        $log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::WARNING));        
        $log->warning('Modena API doRequest called');


        $headers = [];
        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint(), $headers, http_build_query($fields));

        $response = $this->client->send($request);

        if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        return $response;
       
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }


    */

}