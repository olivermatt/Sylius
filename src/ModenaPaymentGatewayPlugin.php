<?php

declare(strict_types=1);

namespace Modena\PaymentGatewayPlugin;


use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

///final class AcmeSyliusExamplePlugin extends Bundle

final class ModenaPaymentGatewayPlugin extends Bundle
{
    use SyliusPluginTrait;
}

