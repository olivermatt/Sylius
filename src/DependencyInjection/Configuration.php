<?php

declare(strict_types = 1);

namespace BuyPlanEstonia\SyliusBuyPlanPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('buyplan_estonia_sylius_buyplan_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
