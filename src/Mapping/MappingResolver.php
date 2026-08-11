<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Bnix\PimcorePrestashopBundle\FieldMapping\FieldMapping;

final class MappingResolver
{
    public function resolve(string $prestashopField, string $value)
    {
        if(str_contains($value, '\\')) {
            return new FieldMapping($prestashopField, MappingType::CUSTOM_MAPPER, $value);
        }

        return new FieldMapping($prestashopField, MappingType::OBJECT_FIELD, $value);
    }
}
