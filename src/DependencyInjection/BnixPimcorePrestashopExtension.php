<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\DependencyInjection;

use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistryFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BnixPimcorePrestashopExtension extends Extension
{
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
