<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\QuantityValue;

final class ClassificationStoreMapper implements MapperInterface
{
    private const FIELD_TYPES = [
        'classificationstore',
    ];

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        $getter = 'get' . ucfirst($field);

        $ret = [];

        /** @var DataObject\Classificationstore $cstore */
        $cstore = $object->$getter();
        foreach($cstore->getGroups() as $group)
        {
            foreach($group->getKeys() as $key)
            {
                $cfg = $key->getConfiguration();

                if(!$key->getValue())
                    continue;

                $value = ($cfg->getType() == 'select') ? explode("_", $key->getValue())[0] : $key->getValue();

                if ($cfg->getType() === 'select') {

                    $definition = json_decode($cfg->getDefinition(), true); // contains options array

                    foreach ($definition['options'] as $option) {
                        if ($option['value'] == $key->getValue()) {
                            $value = $option['key']; // this is the "display name"
                            break;
                        }
                    }
                }

                $featureName = $key->getConfiguration()->getTitle();

                if($value instanceof QuantityValue)
                {
                    $value = $value->getValue();
                }
                else if(is_array($value) && count($value) == 1 && array_is_list($value))
                {
                    $value = $value[0];
                }
                else if(is_array($value) && count($value) > 1 && array_is_list($value))
                {
                    $value = implode(', ', $value);
                }

                $ret[$featureName] = $value;
            }
        }

        return $ret;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        if(!$definition)
            return false;

        return in_array($definition->getFieldType(), self::FIELD_TYPES);
    }
}
