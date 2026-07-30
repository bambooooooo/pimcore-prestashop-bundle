<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Pimcore\Model\DataObject\Concrete;

interface FieldMapperInterface
{
    public function map(Concrete $object, string $prestashopField): mixed;
}
