<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\FieldMapping;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\QuantityValue;

final class QuantityValueFieldMapper implements FieldMapperInterface
{
    public function supports(string $fieldOrMapper, Data|null $definition): bool
    {
        if(!$definition)
            return false;

        return $definition->getFieldType() == 'quantityValue';
    }

    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);
        return $object->$getter()?->getValue();
    }
}
