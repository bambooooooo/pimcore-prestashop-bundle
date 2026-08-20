<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -25)]
final class ClassMapper implements MapperInterface
{
    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
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
