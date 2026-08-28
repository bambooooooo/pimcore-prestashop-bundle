<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject;

interface MapperInterface
{
    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool;
    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed;
    public function type(): string;
}
