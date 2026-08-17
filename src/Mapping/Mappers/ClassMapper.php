<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class ClassMapper implements MapperInterface
{
    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);
        return $object->$getter();
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        $getter = 'get' . ucfirst($fieldOrMapper);
        return !$definition && method_exists($product, $getter);
    }
}
