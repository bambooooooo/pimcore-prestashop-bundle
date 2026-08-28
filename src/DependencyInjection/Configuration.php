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
                                        ->integerNode('id')
                                            ->isRequired()
                                        ->end()
                                        ->arrayNode('languages')
                                            ->isRequired()
                                            ->scalarPrototype()
                                            ->end()
                                        ->end()
                                        ->arrayNode('mappings')
                                            ->arrayPrototype()
                                                ->scalarPrototype()
                                                    ->validate()
                                                        ->ifTrue(static fn (string $value): bool => str_contains($value, '\\') && str_contains($value, ','))
                                                        ->thenInvalid('Mapping value cannot contain a comma (",") and a backslash ("\\") at the same time.')
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()

                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('mappings')
                                ->arrayPrototype()
                                    ->scalarPrototype()
                                        ->validate()
                                            ->ifTrue(static fn (string $value): bool => str_contains($value, '\\') && str_contains($value, ','))
                                            ->thenInvalid('Mapping value cannot contain a comma (",") and a backslash ("\\") at the same time.')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('excluded_parameters')
                                ->scalarPrototype()
                                ->end()
                            ->end()

                        ->end()
                    ->end()
                ->end()
            ->end();

        return  $treeBuilder;
    }
}
