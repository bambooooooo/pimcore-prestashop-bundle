<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\DependencyInjection;

use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Config\StoreRegistryFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BnixPimcorePrestashopExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'entity_managers' => [
                    'bnix_pimcore_prestashop' => [
                        'connection' => 'default',
                        'mappings' => [
                            'BnixPimcorePrestashopBundle' => [
                                'type' => 'attribute',
                                'dir' => 'Entity',
                                'prefix' => 'Bnix\\PimcorePrestashopBundle\\Entity',
                                'alias' => 'BnixPimcorePrestashop',
                                'is_bundle' => true
                            ]
                        ],
                    ]
                ]
            ],
        ]);

        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'Bnix\\PimcorePrestashopBundle\\Migrations' => '@BnixPimcorePrestashopBundle/Migrations',
            ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'bnix_pimcore_prestashop',
            $config
        );

        $container->getDefinition(StoreRegistry::class)->setFactory([
            StoreRegistryFactory::class,
            'create'
        ])->setArgument(0, $config);
    }
}
