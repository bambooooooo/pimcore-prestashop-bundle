<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Registry;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;

final class MapperRegistry
{
    /**
     * @param iterable<MapperInterface> $mappers
     */
    public function __construct(
        private readonly iterable $mappers
    ) { }

    public function getMappers()
    {
        return $this->mappers;
    }
}
