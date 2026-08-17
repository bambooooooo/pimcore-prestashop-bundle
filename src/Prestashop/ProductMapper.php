<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Mapping\MappingResolver;
use Bnix\PimcorePrestashopBundle\Mapping\MappingConfiguration;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;

final class ProductMapper
{
    public function __construct(private readonly MappingResolver $resolver)
    {

    }

    public function map(DataObject $product, StoreConfiguration $store)
    {
        $mapping = MappingConfiguration::fromStore($store, $product->getClassName());

        $values = [];

        $classDef = ClassDefinition::getByName($product->getClassName());

        foreach ($mapping->all() as $prestashopField => $fieldOrMapper)
        {
            $def = $classDef->getFieldDefinition($fieldOrMapper);
            $mapper = $this->resolver->resolve($fieldOrMapper, $def, $product);
            $values[$prestashopField] = $mapper->map($product, $fieldOrMapper);
        }

        return new PrestashopProductData(
            reference: $values['reference'] ?? null,
            name: $values['name'] ?? null,
            description: $values['description'] ?? null,
            supplierReference: $values['supplier_reference'] ?? null,
            price: $values['price'] ?? null,
            image: $values['image'] ?? null,
            images: $values['images'] ?? [],
            Width: $values['width'] ?? null,
            Height: $values['height'] ?? null,
            Depth: $values['depth'] ?? null,
            Mass: $values['weight'] ?? null
        );
    }
}
