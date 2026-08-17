<?php

namespace Bnix\PimcorePrestashopBundle\Mapping;

use Bnix\PimcorePrestashopBundle\Mapping\Mappers\LiteralMapper;
use Bnix\PimcorePrestashopBundle\Registry\MapperRegistry;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class MappingResolver
{
    public function __construct(private readonly MapperRegistry $registry)
    {

    }

    public function resolve(string $fieldOrMapper, Data|null $definition, DataObject $product): MapperInterface
    {
        if(str_contains($fieldOrMapper, "\\"))
        {
            if(class_exists($fieldOrMapper) && is_a($fieldOrMapper, MapperInterface::class, true))
            {
                return new $fieldOrMapper();
            }

            $fieldTypeNote = $definition ? " of type '{$definition->getFieldType()}'" : '';

            throw new \RuntimeException("No mapper found for definition '$fieldOrMapper'$fieldTypeNote.");
        }

        foreach ($this->registry as $mapper) {

            if($mapper->supports($fieldOrMapper, $definition, $product)) {
                return $mapper;
            }
        }

        return new LiteralMapper();
    }
}
