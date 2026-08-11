<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Registry\FieldMapperRegistry;
use Bnix\PimcorePrestashopBundle\Mapping\MappingConfiguration;
use Pimcore\Model\DataObject;

final class ProductMapper
{
    public function __construct(private readonly FieldMapperRegistry $registry)
    {

    }

    public function map(DataObject $product, StoreConfiguration $store)
    {
        $mapping = MappingConfiguration::fromStore($store, $product->getClassName());

        $values = [];

        foreach ($mapping->all() as $prestashopField => $definition)
        {
            $mapper = $this->registry->resolve($definition);
            $values[$prestashopField] = $mapper->map($product, $definition);
        }

        return new PrestashopProductData(
            reference: $values['reference'] ?? null,
            name: $values['name'] ?? null,
            supplierReference: $values['supplier_reference'] ?? null,
            price: $values['price'] ?? null,
            images: $values['images'] ?? [],
        );
    }
}
