<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation;

final class AssetMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'manyToOneRelation',
    ];

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        $getter = 'get' . ucfirst($field);
        return $object->$getter()?->getId();
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(!$definition)
            return false;

        if(!in_array($definition->getFieldType(), self::FIELD_TYPES))
            return false;

        /** @var ManyToOneRelation $definition */
        $isAssetAllowed = $definition->getAssetsAllowed();
        $documentAllowed = $definition->getDocumentsAllowed();
        $objectsAllowed = $definition->getObjectsAllowed();

        return $isAssetAllowed && !$documentAllowed && !$objectsAllowed;
    }
}
