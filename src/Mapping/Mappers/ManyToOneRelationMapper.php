<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Scalar;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -10)]
final class ManyToOneRelationMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'manyToOneRelation'
    ];

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        $objectFields = ['Name', 'Title', 'Code', 'Sku'];
        $getter = 'get' . ucfirst($field);

        $element = $object->$getter();

        if(!$element)
        {
            return null;
        }

        foreach($objectFields as $field)
        {
            $innerGetter = 'get' . ucfirst($field);

            if(method_exists($element, $innerGetter))
            {
                return $element->$innerGetter();
            }
            else
            {
                return null;
            }
        }
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }

    public function type(): string
    {
        return Scalar::class;
    }
}
