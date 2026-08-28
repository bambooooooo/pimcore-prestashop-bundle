<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Scalar;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -50)]
final class ObjectFieldMapper implements MapperInterface
{
    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        $getterChunks = explode('~', $field);

        $actual = $object;

        foreach ($getterChunks as $getterChunk)
        {
            $getter = 'get' . ucfirst($getterChunk);
            $actual = $actual->$getter();
        }

        return (string)$actual;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(class_exists($fieldOrMapper))
            return false;

        $actual = $product;

        $getterChunks = explode('~', $fieldOrMapper);

        foreach ($getterChunks as $getterChunk)
        {
            $getter = 'get' . ucfirst($getterChunk);
            if(!method_exists($actual, $getter))
            {
                return false;
            }

            $actual = $actual->$getter();
        }

        return true;
    }

    public function type(): string
    {
        return Scalar::class;
    }
}
