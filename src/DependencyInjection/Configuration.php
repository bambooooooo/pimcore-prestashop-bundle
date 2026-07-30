<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pimcore_prestashop');
        $root = $treeBuilder->getRootNode();

        $root->
            children()
                ->arrayNode('stores')
                    ->isRequired()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('url')
                                ->isRequired()
                                ->end()
                            ->scalarNode('host')
                                ->end()
                            ->scalarNode('api_key')
                                ->isRequired()
                                ->end()
                            ->arrayNode('languages')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('currencies')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('multistore')
                                ->arrayPrototype()
                                    ->children()
                                        ->integerNode('shop_id')
                                        ->isRequired()
                                        ->end()
                                        ->integerNode('shop_group_id')
                                        ->isRequired()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('mappings')
                                ->arrayPrototype()
                                    ->scalarPrototype()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return  $treeBuilder;
    }
}
