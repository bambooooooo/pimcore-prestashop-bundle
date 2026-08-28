<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Bnix\PimcorePrestashopBundle\Mapping\Types\ScalarList;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;

final class ImageGalleryMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'imageGallery',
    ];

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        $getter = 'get' . ucfirst($field);

        /** @var ImageGallery $object */
        $field = $object->$getter();

        $imageAssetIds = [];

        /** @var Hotspotimage $image */
        foreach($field as $image) {
            $imageAssetIds[] = $image->getImage()->getId();
        }

        return $imageAssetIds;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }

    public function type(): string
    {
        return ScalarList::class;
    }
}
