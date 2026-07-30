<?php

declare(strict_types=1);

namespace Pimcore\PimcorePrestashopBundle\Bundle\Registry;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapping;
use Bnix\PimcorePrestashopBundle\Mapping\MappingResolver;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;

final class MappingRegistry
{
    /**
     * @var array<string,array<string,FieldMapping>>
     */
    private array $mappings = [];

    public function __construct(MappingResolver $resolver, StoreRegistry $stores)
    {
        foreach($stores->all() as $store) {
            foreach ($store->getMappings() as $class => $fields) {
                foreach($fields as $prestashopField => $source)
                {
                    $this->mappings[$store->getName()][$class][$prestashopField] =
                        $resolver->resolve($prestashopField, $source);
                }


            }
        }
    }

    /**
     * @return array<string,FieldMapping>
     */
    public function get(string $store, string $class): array
    {
        return $this->mappings[$store][$class] ?? [];
    }
}
