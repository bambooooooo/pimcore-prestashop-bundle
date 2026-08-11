<?php

namespace Bnix\PimcorePrestashopBundle\FieldMapping;

use Bnix\PimcorePrestashopBundle\Mapping\MappingType;

final class FieldMapping
{
    public function __construct(
        public string $prestashopField,
        public MappingType $mappingType,
        public string $value
    )
    {

    }
}
