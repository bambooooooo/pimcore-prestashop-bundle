<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Pimcore\Model\DataObject;

interface FieldMapperInterface
{
    public function supports(string $field): bool;
    public function map(DataObject $object, string $field): mixed;
}
