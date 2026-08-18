<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Storage;

use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;

interface ExternalProductReferenceStorageInterface
{
    public function find(int $objectId, string $systemName): ?ExternalProductReference;

    public function save(int $objectId, string $systemName, string $externalId, string $hash): void;

    public function delete(int $objectId, string $systemName): void;
}
