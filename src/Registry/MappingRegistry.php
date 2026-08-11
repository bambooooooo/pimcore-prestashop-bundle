<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Registry;

use Bnix\PimcorePrestashopBundle\FieldMapping\FieldMapping;
use Bnix\PimcorePrestashopBundle\Mapping\MappingResolver;

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
}
