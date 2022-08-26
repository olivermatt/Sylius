<?php

declare(strict_types = 1);

namespace ModenaEstonia\SyliusModenaPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('Modena_estonia_sylius_Modena_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
