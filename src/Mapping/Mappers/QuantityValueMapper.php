<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\QuantityValue;

final class QuantityValueMapper implements MapperInterface
{
    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
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
