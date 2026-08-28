<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

use Bnix\PimcorePrestashopBundle\Config\MultiStore;
use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Mapping\MappingResolver;
use Bnix\PimcorePrestashopBundle\Mapping\MappingConfiguration;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Scalar;
use Bnix\PimcorePrestashopBundle\Mapping\Types\ScalarList;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Localized;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Parameters;


final class ProductMapper
{
    public function __construct(private readonly MappingResolver $resolver)
    {

    }

    public function map(DataObject $product, StoreConfiguration|MultiStore $store)
    {
        $mapping = MappingConfiguration::fromStore($store, $product->getClassName());
        $languages = $store->getLanguages();
        $defaultLangId = $store->getDefaultLanguage();

        $values = [];

        $classDef = ClassDefinition::getByName($product->getClassName());
        $localizedFields = $this->getLocalizedFields($classDef);

        foreach ($mapping->all() as $prestashopField => $fieldOrMapper)
        {
            $chunks = explode(',', $fieldOrMapper);
            $chunks = array_filter($chunks, fn($chunk) => trim($chunk) !== '');
            $chunks = array_map(fn ($chunk) => trim($chunk), $chunks);

            $finalType = null;

            foreach($chunks as $expression)
            {
                $def = $classDef->getFieldDefinition($expression);
                $isLocalized = $def && in_array($expression, $localizedFields);

                $mapper = $this->resolver->resolve($expression, $def, $product);
                $type = $isLocalized ? Localized::class : $mapper->type();

                $finalType = $this->obtainFieldType($finalType, $type);
            }

            $obj = new $finalType(null, $chunks[count($chunks)-1], $languages);

            foreach($chunks as $expression)
            {
                $def = $classDef->getFieldDefinition($expression);
                $isLocalized = $def && in_array($expression, $localizedFields);
                $mapper = $this->resolver->resolve($expression, $def, $product);
                $value = $mapper->map($product, $expression, $languages, $isLocalized);

                if($value == null)
                    continue;

                $type = $isLocalized ? Localized::class : $mapper->type();
                $valueObject = new $type($value, $expression, $languages);

                $obj = $obj->concat($valueObject);
            }

            $values[$prestashopField] = $obj->value;
        }

        return new PrestashopProductData(
            reference: $this->asScalar($values['reference'] ?? null, $defaultLangId),
            name: $this->asLocalized($values['name'] ?? null, $languages),
            description: $this->asLocalized($values['description'] ?? null, $languages),
            descriptionShort: $this->asLocalized($values['description_short'] ?? null, $languages),
            supplierReference: $this->asScalar($values['supplier_reference'] ?? null, $defaultLangId),
            price: $this->asScalar($values['price'] ?? null, $defaultLangId),
            images: $this->asList($values['images'] ?? []),
            width: $this->asScalar($values['width'] ?? null, $defaultLangId),
            height: $this->asScalar($values['height'] ?? null, $defaultLangId),
            depth: $this->asScalar($values['depth'] ?? null, $defaultLangId),
            mass: $this->asScalar($values['mass'] ?? null, $defaultLangId),
            ean: $this->asScalar($values['ean'] ?? null, $defaultLangId),
            isbn: $this->asScalar($values['isbn'] ?? null, $defaultLangId),
            upc: $this->asScalar($values['upc'] ?? null, $defaultLangId),
            mpn: $this->asScalar($values['mpn'] ?? null, $defaultLangId),
            metaDescription: $this->asLocalized($values['meta_description'] ?? null, $languages),
            metaTitle: $this->asLocalized($values['meta_title'] ?? null, $languages),
            linkRewrite: $this->asLocalized($values['link_rewrite'] ?? null, $languages, true),
            files: $this->asList($values['files'] ?? []),
            parameters: $values['parameters'] ?? [],
            id_manufacturer: $this->asScalar($values['id_manufacturer'] ?? null, $defaultLangId),
            id_supplier: $this->asScalar($values['id_supplier'] ?? null, $defaultLangId),
            id_category_default: $this->asScalar($values['id_category_default'] ?? null, $defaultLangId),
            type: $this->asScalar($values['type'] ?? null, $defaultLangId),
            location: $this->asScalar($values['location'] ?? null, $defaultLangId),
            additional_delivery_times: $this->asScalar($values['additional_delivery_times'] ?? null, $defaultLangId),
            delivery_in_stock: $this->asScalar($values['delivery_in_stock'] ?? null, $defaultLangId),
            delivery_out_of_stock: $this->asScalar($values['delivery_out_of_stock'] ?? null, $defaultLangId),
            on_sale: $this->asScalar($values['on_sale'] ?? null, $defaultLangId),
            online_only: $this->asScalar($values['online_only'] ?? null, $defaultLangId),
            minimum_quantity: $this->asScalar($values['minimum_quantity'] ?? null, $defaultLangId),
            low_stock_threshold: $this->asScalar($values['low_stock_threshold'] ?? null, $defaultLangId),
            low_stock_alert: $this->asScalar($values['low_stock_alert'] ?? null, $defaultLangId),
            unity: $this->asScalar($values['unity'] ?? null, $defaultLangId),
            unit_price_ratio: $this->asScalar($values['unit_price_ratio'] ?? null, $defaultLangId),
            additional_shipping_cost: $this->asScalar($values['additional_shipping_cost'] ?? null, $defaultLangId),
            available_for_order: $this->asScalar($values['available_for_order'] ?? null, $defaultLangId),
            show_condition: $this->asScalar($values['show_condition'] ?? null, $defaultLangId),
            condition: $this->asScalar($values['condition'] ?? null, $defaultLangId),
            show_price: $this->asScalar($values['show_price'] ?? null, $defaultLangId),
            visibility: $this->asScalar($values['visibility'] ?? null, $defaultLangId),
            meta_keywords: $this->asLocalized($values['meta_keywords'] ?? null, $languages),
            advanced_stock_management: $this->asScalar($values['advanced_stock_management'] ?? null, $defaultLangId),
            stock_pack_type: $this->asScalar($values['stock_pack_type'] ?? null, $defaultLangId),
            active: $this->asScalar($values['active'] ?? null, $defaultLangId),
//            cache_default_attribute: null,
//            id_tax_rules_group: null,
//            quantity_discount: null,
//            ecotax: null,
        );
    }

    private function obtainFieldType(?string $type, ?string $anotherType)
    {
        if($type == null)
            return $anotherType;

        if($type == $anotherType)
            return $type;

        $allowedCombinations = [
            Scalar::class => [
                Scalar::class => Scalar::class,
                Localized::class => Localized::class,
                ScalarList::class => ScalarList::class,
                Parameters::class => Parameters::class
            ],
            Localized::class => [
                Localized::class => Localized::class,
                ScalarList::class => ScalarList::class,
                Parameters::class => Parameters::class
            ],
            ScalarList::class => [
                ScalarList::class => ScalarList::class,
            ],
            Parameters::class => [
                Parameters::class => Parameters::class
            ]
        ];

        if(isset($allowedCombinations[$type][$anotherType]))
            return $allowedCombinations[$type][$anotherType];

        if(isset($allowedCombinations[$anotherType][$type]))
            return $allowedCombinations[$anotherType][$type];

        throw new PrestashopException("Incompatible field type '{$type}' with '{$anotherType}'.");
    }

    private function asScalar(mixed $value, int $defaultLangId): string|int|float|bool|null
    {
        if($value === null)
            return null;

        if(!is_array($value))
        {
            return $value;
        }

        if(array_is_list($value))
        {
            return implode('', $value);
        }

        return $value[$defaultLangId];
    }

    private function asLocalized(mixed $value, array $storeLanguages, $asSlug = false): array|null
    {
        if($value === null)
            return null;

        if(!is_array($value))
        {
            $ret = [];
            $slug = $this->slugify($value);

            foreach($storeLanguages as $lang => $langId)
            {
                $ret[$langId] = $slug;
            }

            return $ret;
        }

        if(array_is_list($value))
        {
            throw new \RuntimeException("Cannot map localized field to array.");
        }

        foreach ($value as $langId => $v)
        {
            $value[$langId] = $asSlug ? $this->slugify($v) : $v;
        }

        return $value;
    }

    private function asList(mixed $value): array
    {
        if($value === null)
            return [];

        if(!is_array($value))
        {
            return [$value];
        }

        if(array_is_list($value))
        {
            return $value;
        }

        return array_values($value);
    }

    private function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));

        $value = preg_replace('/[\s_\/+]+/', '-', $value);
        $value = preg_replace('/[^a-z0-9-]/', '', $value);
        $value = preg_replace('/-+/', '-', $value);

        return trim($value, '-');
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
