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
        $localizedFields = $this->getLocalizedFields($classDef);

        foreach ($mapping->all() as $prestashopField => $fieldOrMapper)
        {
            $def = $classDef->getFieldDefinition($fieldOrMapper);
            $isLocalized = $def && in_array($fieldOrMapper, $localizedFields);
            $mapper = $this->resolver->resolve($fieldOrMapper, $def, $product);

            $values[$prestashopField] = $mapper->map($product, $fieldOrMapper, $store->getLanguages(), $isLocalized);
        }

        $defaultLangId = $store->getLanguages()[array_key_first($store->getLanguages())];

        return new PrestashopProductData(
            referencePrefix: in_array( 'reference_prefix', $values, true) ? (is_array($values['reference_prefix']) ? $values['reference_prefix'][$defaultLangId] : $values['reference_prefix']) : null,
            reference: in_array('reference', $values, true) ? (is_array($values['reference']) ? $values['reference'][$defaultLangId] : $values['reference']) : null,
            name: $values['name'] ?? null,
            description: $values['description'] ?? null,
            descriptionShort: $values['description_short'] ?? null,
            supplierReference: $values['supplier_reference'] ?? null,
            price: $values['price'] ?? null,
            images: $values['images'] ?? [],
            width: in_array('width', $values, true) ? (float)$values['width'] : null,
            height: in_array('height', $values, true) ? (float)$values['height'] : null,
            depth: in_array('depth', $values, true) ? (float)$values['depth'] : null,
            mass: in_array('weight', $values, true) ? (float)$values['weight'] : null,
            ean: $values['ean'] ?? null,
            isbn: $values['isbn'] ?? null,
            upc: $values['upc'] ?? null,
            mpn: $values['mpn'] ?? null,
        );
    }

    private function getLocalizedFields(ClassDefinition $classDef): array
    {
        $localizedFields = [];
        $localizedDef = $classDef->getFieldDefinition('localizedfields');

        if(!$localizedDef)
            return [];

        foreach($localizedDef->getChildren() as $child)
        {
            $localizedFields[] = $child->getName();
        }

        foreach($localizedDef->getReferencedFields() as $child)
        {
            foreach ($child->getChildren() as $item)
            {
                $localizedFields[] = $item->getName();
            }
        }

        return $localizedFields;
    }
}
