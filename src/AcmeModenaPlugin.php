<?php

declare(strict_types=1);

///namespace Acme\SyliusExamplePlugin;
namespace Acme\ModenaPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

///final class AcmeSyliusExamplePlugin extends Bundle

final class AcmeModenaPlugin extends Bundle
{
    use SyliusPluginTrait;
}

