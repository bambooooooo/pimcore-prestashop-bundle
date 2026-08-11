<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Registry;

use Bnix\PimcorePrestashopBundle\Mapping\FieldMapperInterface;
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

    public function resolve(string $definition): FieldMapperInterface
    {
        foreach ($this->mappers as $mapper) {

            if($mapper->supports($definition)) {
                return $mapper;
            }
        }

        throw new \RuntimeException("No mapper found for definition '$definition'.");
    }
}
