<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\FieldMapping;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\Hotspotimage;

final class ImageGalleryMapper implements FieldMapperInterface
{
    private const FIELD_TYPES = [
        'imageGallery',
    ];

    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);

        /** @var Image $object */
        $field = $object->$getter();

        $urls = [];

        /** @var Hotspotimage $image */
        foreach($field as $image) {
            $urls[] = $image->getImage()->getRealFullPath(); // TODO: reduce to prestashop's default 2MB with caching
        }

        return $urls;
    }

    public function supports(string $fieldOrMapper, Data|null $definition): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }
}
