<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class ManyToOneRelationMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'manyToOneRelation'
    ];

    public function map(DataObject $object, string $field): mixed
    {
        $objectFields = ['Name', 'Title', 'Code', 'Sku'];

        $getter = 'get' . ucfirst($field);

        foreach($objectFields as $field)
        {
            $innerGetter = 'get' . ucfirst($field);

            if(method_exists($object->$getter(), $innerGetter))
            {
                return $object->$getter()->$innerGetter();
            }
        }

        return $object->$getter()->getKey();
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }
}
