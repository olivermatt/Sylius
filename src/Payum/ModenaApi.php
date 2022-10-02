<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum;
use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
    protected $options = [];




    /** @var string */
    private $apiKey;

    /*
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    */

    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory, string $apiKey)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
        $this->apiKey = $apiKey;
    }

    protected function doRequest($method, array $fields)
    {
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

    protected function getApiEndpoint()
    {
        return "https://google.com";
    }



    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}