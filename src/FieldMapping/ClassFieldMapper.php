<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\FieldMapping;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class ClassFieldMapper implements FieldMapperInterface
{
    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);
        return $object->$getter();
    }

    public function supports(string $fieldOrMapper, Data|null $definition): bool
    {
        return !$definition;
    }
}
