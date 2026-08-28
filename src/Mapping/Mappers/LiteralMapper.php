<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Bnix\PimcorePrestashopBundle\Mapping\Types\Scalar;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsTaggedItem(priority: -100)]
class LiteralMapper implements MapperInterface
{
    public function __construct()
    {

    }

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        return $field;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        return true;
    }

    public function type(): string
    {
        return Scalar::class;
    }
}
