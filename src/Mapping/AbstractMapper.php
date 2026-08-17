<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class AbstractMapper implements MapperInterface
{
    public function supports(string $fieldOrMapper, ?Data $definition, DataObject $product): bool
    {
        return true;
    }
}
