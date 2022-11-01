<?php

declare(strict_types=1);

namespace Modena\PaymentGatewayPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('modena_payment_gateway_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
