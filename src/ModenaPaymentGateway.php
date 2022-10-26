<?php

declare(strict_types=1);

namespace Modena\PaymentGateway;


use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

///final class AcmeSyliusExamplePlugin extends Bundle

final class ModenaPaymentGateway extends Bundle
{
    use SyliusPluginTrait;
}

