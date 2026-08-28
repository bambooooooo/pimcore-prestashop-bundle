<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Scalar;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class ScalarMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'input',
        'textarea',
        'numeric',
        'checkbox',
        'select',
        'date',
        'datetime',
        'time',
        'wysiwyg'
    ];

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        if(!$isLocalized)
        {
            $getter = 'get' . ucfirst($field);
            return $object->$getter();
        }

        $ret = [];
        foreach($languages as $language => $languageId)
        {
            $getter = 'get' . ucfirst($field);
            $ret[$languageId] = $object->$getter($language);
        }

        return $ret;
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
