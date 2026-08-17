<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class LiteralMapper implements MapperInterface
{
    public function map(DataObject $object, string $field): mixed
    {
        return $field;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        return true;
    }
}
