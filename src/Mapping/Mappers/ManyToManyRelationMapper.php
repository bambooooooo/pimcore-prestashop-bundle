<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -10)]
final class ManyToManyRelationMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'manyToManyRelation'
    ];

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        $genericGetterNames = ['Name', 'Title', 'Code', 'Sku', 'Key', 'Id'];

        $getter = 'get' . ucfirst($field);
        $manyToManyField = $object->$getter();

        if(!$manyToManyField)
            return [];

        $ret = [];

        foreach($manyToManyField as $relation)
        {
            foreach($genericGetterNames as $innerGetterName)
            {
                $innerGetter = 'get' . ucfirst($innerGetterName);

                if(method_exists($relation, $innerGetter))
                {
                    $ret[] = $relation->$innerGetter();
                    break;
                }
            }
        }

        return $ret;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }
}
