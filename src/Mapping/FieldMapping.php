<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

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
