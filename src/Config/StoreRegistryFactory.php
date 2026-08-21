<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Config;

use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;

final class StoreRegistryFactory
{
    public static function create(array $config): StoreRegistry
    {
        $stores = [];

        foreach ($config['stores'] ?? [] as $name => $store) {
            $stores[$name] = new StoreConfiguration(
                $name,
                $store['url'],
                $store['host'] ?? str_replace("http://", "", str_replace("https://", "", $store['url'])),
                $store['api_key'],
                $store['languages'] ?? [],
                $store['currencies'] ?? [],
                $store['multistore'] ?? [],
                $store['mappings'] ?? [],
                $store['excluded_parameters'] ?? [],
            );
        }

        return new StoreRegistry($stores);
    }
}
