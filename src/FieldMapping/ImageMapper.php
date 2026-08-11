<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\FieldMapping;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class ImageMapper implements FieldMapperInterface
{
    private const FIELD_TYPES = [
        'image',
    ];

    public function map(DataObject $object, string $field): mixed
    {
        $getter = 'get' . ucfirst($field);

        /** @var Image $object */
        $field = $object->$getter();

        return $field->getRealFullPath();
    }

    public function supports(string $fieldOrMapper, Data|null $definition): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }
}
