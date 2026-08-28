<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Config;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopAssociationDefinition;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopFieldDefinition;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopSchemaDefinition;
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
                self::parseMultiStores($store['multistore'] ?? [], $store['languages'] ?? []),
                $store['mappings'] ?? [],
                $store['excluded_parameters'] ?? []
            );
        }

        return new StoreRegistry($stores);
    }

    private static function parseMultiStores(array $data, array $prestaLanguages): array
    {
        if(!$data || empty($data)) {
            return [];
        }

        $ret = [];


        foreach($data as $name => $config)
        {
            $languages = [];
            foreach($config['languages'] ?? [] as $lang)
            {
                $languages[$lang] = $prestaLanguages[$lang];
            }

            $ret[$name] = new MultiStore((int)$config['id'], $name, $languages, $config['mappings']);
        }

        return $ret;
    }
}
