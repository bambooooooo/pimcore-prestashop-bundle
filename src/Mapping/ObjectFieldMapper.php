<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Pimcore\Model\DataObject;

final class ObjectFieldMapper implements FieldMapperInterface
{
    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);
        return $object->$getter();
    }

    public function supports(string $field): bool
    {
        return !class_exists($field);
    }
}
