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
            if(!str_contains($fieldOrMapper, ","))
            {
                $def = $classDef->getFieldDefinition($fieldOrMapper);
                $isLocalized = $def && in_array($fieldOrMapper, $localizedFields);
                $mapper = $this->resolver->resolve($fieldOrMapper, $def, $product);
                $values[$prestashopField] = $mapper->map($product, $fieldOrMapper, $store->getLanguages(), $isLocalized);
            }
            else
            {
                $mapperNames = explode(',', str_replace(' ', '', $fieldOrMapper));
                $mapperNames = array_filter($mapperNames, fn($name) => $name !== '');

                $mergedValue = [];

                foreach($mapperNames as $mapperName)
                {
                    $def = $classDef->getFieldDefinition($mapperName);
                    $isLocalized = ($def && in_array($mapperName, $localizedFields) || str_contains($mapperName, '~'));

                    $mapper = $this->resolver->resolve($mapperName, $def, $product);

                    $currentValue = $mapper->map($product, $mapperName, $store->getLanguages(), $isLocalized);

                    if(count($mergedValue) == 0 && is_array($currentValue) && !array_is_list($currentValue))
                    {
                        foreach($store->getLanguages() as $lang => $langId)
                        {
                            $mergedValue[$langId] = ($mergedValue[$langId] ?? "") . ($currentValue[$langId] ?? "");
                        }
                    }
                    else if (count($mergedValue) > 0 && is_array($mergedValue) && !array_is_list($mergedValue))
                    {
                        if(is_array($currentValue) && !array_is_list($currentValue))
                        {
                            foreach($store->getLanguages() as $lang => $langId)
                            {
                                $mergedValue[$langId] = ($mergedValue[$langId] ?? "") . ($currentValue[$langId] ?? "");
                            }
                        }
                        else
                        {
                            foreach($store->getLanguages() as $lang => $langId)
                            {
                                $mergedValue[$langId] = ($mergedValue[$langId] ?? "") . ($currentValue ?? "");
                            }
                        }
                    }
                    else
                    {
                        $currentValue = is_array($currentValue) ? $currentValue : [$currentValue];
                        $mergedValue = array_merge($mergedValue, $currentValue);
                    }
                }

                $values[$prestashopField] = $mergedValue;
            }
        }

        $defaultLangId = $store->getLanguages()[array_key_first($store->getLanguages())];

        return new PrestashopProductData(
            reference: $this->asScalar($values['reference'] ?? null, $defaultLangId),
            name: $this->asLocalized($values['name'] ?? null, $store->getLanguages()),
            description: $this->asLocalized($values['description'] ?? null, $store->getLanguages()),
            descriptionShort: $this->asLocalized($values['description_short'] ?? null, $store->getLanguages()),
            supplierReference: $this->asScalar($values['supplier_reference'] ?? null, $defaultLangId),
            price: $values['price'] ?? null,
            images: $this->asList($values['images'] ?? []),
            width: $this->asScalar($values['width'] ?? null, $defaultLangId),
            height: $this->asScalar($values['height'] ?? null, $defaultLangId),
            depth: $this->asScalar($values['depth'] ?? null, $defaultLangId),
            mass: $this->asScalar($values['mass'] ?? null, $defaultLangId),
            ean: $this->asScalar($values['ean'] ?? null, $defaultLangId),
            isbn: $this->asScalar($values['isbn'] ?? null, $defaultLangId),
            upc: $this->asScalar($values['upc'] ?? null, $defaultLangId),
            mpn: $this->asScalar($values['mpn'] ?? null, $defaultLangId),
            metaDescription: $this->asLocalized($values['meta_description'] ?? null, $store->getLanguages()),
            metaTitle: $this->asLocalized($values['meta_title'] ?? null, $store->getLanguages()),
            linkRewrite: $this->asLocalized($values['link_rewrite'] ?? null, $store->getLanguages(), true),
        );
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
