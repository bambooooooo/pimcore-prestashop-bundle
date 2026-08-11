<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;

final class MappingConfiguration
{
    /**
     * @param array<string,string> $mapping
     */
    public function __construct(
        private readonly array $mapping,
    ) {
    }


    public static function fromStore(StoreConfiguration $store, string $className): self {

        if( !isset($store->getMappings()[$className]))
        {
            throw new \RuntimeException("No mapping found for class '$className'.");
        }

        return new self(
            $store->getMappings()[$className]
        );
    }

    public function all():array
    {
        return $this->mapping;
    }

    public function get(string $prestashopField): ?string
    {
        return $this->mapping[$prestashopField] ?? null;
    }
}
