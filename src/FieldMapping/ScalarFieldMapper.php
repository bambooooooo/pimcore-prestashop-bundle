<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\FieldMapping;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class ScalarFieldMapper implements FieldMapperInterface
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

    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);
        return $object->$getter();
    }

    public function supports(string $fieldOrMapper, Data|null $definition): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }
}
