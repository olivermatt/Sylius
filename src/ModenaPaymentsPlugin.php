<?php

declare(strict_types=1);

namespace Modena\PaymentsPlugin;
///namespace Acme\SyliusExamplePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

///final class AcmeSyliusExamplePlugin extends Bundle

final class ModenaPaymentsPlugin extends Bundle
{
    use SyliusPluginTrait;
}

