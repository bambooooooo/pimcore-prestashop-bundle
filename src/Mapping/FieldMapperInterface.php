<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject;

interface FieldMapperInterface
{
    public function supports(string $fieldOrMapper, Data|null $definition): bool;
    public function map(DataObject $object, string $field): mixed;
}
