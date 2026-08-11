<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Registry;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class FieldMapperRegistry
{
    /**
     * @param iterable<FieldMapperInterface> $mappers
     */
    public function __construct(
        #[TaggedIterator('bnix.pimcore_prestashop.field_mapper')]
        private readonly iterable $mappers,
    ) {
    }

    public function resolve(string $fieldOrMapper, Data|null $definition): FieldMapperInterface
    {
        foreach ($this->mappers as $mapper) {

            if($mapper->supports($fieldOrMapper, $definition)) {
                return $mapper;
            }
        }

        $fieldTypeNote = $definition ? " of type '{$definition->getFieldType()}'" : '';

        throw new \RuntimeException("No mapper found for definition '$fieldOrMapper'$fieldTypeNote.");
    }
}
