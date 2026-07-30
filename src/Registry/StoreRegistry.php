<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Registry;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use RuntimeException;

final class StoreRegistry
{
    /**
     * @var array<string,StoreConfiguration>
     */
    private array $stores = [];


    /**
     * @param StoreConfiguration[] $stores
     */
    public function __construct(array $stores)
    {
        foreach ($stores as $store) {
            $this->stores[$store->getName()] = $store;
        }
    }


    public function get(string $name): StoreConfiguration
    {
        if (!isset($this->stores[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Prestashop store "%s" does not exist.',
                    $name
                )
            );
        }

        return $this->stores[$name];
    }


    public function has(string $name): bool
    {
        return isset($this->stores[$name]);
    }


    /**
     * @return StoreConfiguration[]
     */
    public function all(): array
    {
        return array_values($this->stores);
    }
}
