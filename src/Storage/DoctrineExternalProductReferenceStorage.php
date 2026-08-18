<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Storage;

use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;
use Bnix\PimcorePrestashopBundle\Repository\ExternalProductReferenceRepository;

final class DoctrineExternalProductReferenceStorage implements ExternalProductReferenceStorageInterface
{
    public function __construct(private readonly ExternalProductReferenceRepository $repository)
    {

    }

    public function find(int $objectId, string $systemName): ?ExternalProductReference
    {
        return $this->repository->findOne(
            objectId: $objectId,
            systemName: $systemName,
        );
    }

    public function save(int $objectId, string $systemName, string $externalId, string $hash): void
    {
        $mapping = $this->repository->findOne(
            objectId: $objectId,
            systemName: $systemName,
        );

        if($mapping == null)
        {
            $mapping = new ExternalProductReference($objectId, $systemName, $externalId, $hash);
        }
        else
        {
            $mapping->setExternalId($externalId);
        }

        $this->repository->save($mapping);
    }

    public function delete(int $objectId, string $systemName): void
    {
        $mapping = $this->repository->findOne(
            objectId: $objectId,
            systemName: $systemName,
        );

        if($mapping != null)
        {
            $this->repository->delete($mapping);
        }
    }
}
