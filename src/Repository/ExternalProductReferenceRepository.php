<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Repository;

use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;
use Bnix\PimcorePrestashopBundle\Storage\ExternalProductReferenceStorageInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


final class ExternalProductReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalProductReference::class);
    }

    public function findOne(int $objectId, string $systemName): ?ExternalProductReference
    {
        return $this->findOneBy([
            'objectId' => $objectId,
            'systemName' => $systemName
        ]);
    }

    /**
     * @return array|ExternalProductReference[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(ExternalProductReference $externalReference): void
    {
        $this->getEntityManager()->persist($externalReference);
        $this->getEntityManager()->flush();
    }

    public function delete(ExternalProductReference $externalReference): void
    {
        $this->getEntityManager()->remove($externalReference);
        $this->getEntityManager()->flush();
    }
}
