<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Entity;

use Bnix\PimcorePrestashopBundle\Repository\ExternalProductReferenceRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExternalProductReferenceRepository::class)]
#[ORM\Table(
    name: 'bnix_external_reference',
)]
class ExternalProductReference
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Pimcore's DataObject id
     *
     * @var int
     */
    #[ORM\Column(name: 'object_id', type: 'integer')]
    private int $objectId;

    /**
     * External system name for unique identification
     *
     * @var string
     */
    #[ORM\Column(name: 'system_name', type: 'string', length: 255)]
    private string $systemName;

    /**
     * External system id - uuid / string / int / int-int pair
     * @var string
     */
    #[ORM\Column(name: 'external_id', type: 'string', length: 255)]
    private string $externalId;

    #[ORM\Column(name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at')]
    private DateTime $updatedAt;

    /**
     * Last integration checksum as sha256 of integrated part (not whole DataObject)
     * @var string
     */
    #[ORM\Column(name: 'hash', type: 'string', length: 64)]
    private string $hash;

    /**
     * General purpose hash 2
     * @var string|null
     */
    #[ORM\Column(name: 'hash_2', type: 'string', length: 64)]
    private ?string $hash2;

    /**
     * General purpose hash 3
     * @var string|null
     */
    #[ORM\Column(name: 'hash_3', type: 'string', length: 64)]
    private ?string $hash3;

    /**
     * General purpose hash 4
     * @var string|null
     */
    #[ORM\Column(name: 'hash_4', type: 'string', length: 64)]
    private ?string $hash4;

    public function __construct(int $objectId, string $systemName, string $externalId, string $hash, string $hash2 = null, string $hash3 = null, string $hash4 = null)
    {
        $this->objectId = $objectId;
        $this->systemName = $systemName;
        $this->externalId = $externalId;
        $this->hash = $hash;
        $this->hash2 = $hash2;
        $this->hash3 = $hash3;
        $this->hash4 = $hash4;

        $now = new DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getHash2(): string|null
    {
        return $this->hash2;
    }

    public function getHash3(): string|null
    {
        return $this->hash3;
    }

    public function getHash4(): string|null
    {
        return $this->hash4;
    }

    public function getSystemName(): string
    {
        return $this->systemName;
    }

    public function setExternalId(string $externalId, string $hash): void
    {
        $this->externalId = $externalId;
        $this->hash = $hash;
        $this->updatedAt = new DateTime();
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
        $this->updatedAt = new DateTime();
    }

    public function setHash2(string $hash): void
    {
        $this->hash2 = $hash;
        $this->updatedAt = new DateTime();
    }

    public function setHash3(string $hash): void
    {
        $this->hash3 = $hash;
        $this->updatedAt = new DateTime();
    }

    public function setHash4(string $hash): void
    {
        $this->hash = $hash;
        $this->updatedAt = new DateTime();
    }
}
