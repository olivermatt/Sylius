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
    
}