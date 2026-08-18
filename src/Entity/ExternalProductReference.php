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

    public function __construct(int $objectId, string $systemName, string $externalId, string $hash)
    {
        $this->objectId = $objectId;
        $this->systemName = $systemName;
        $this->externalId = $externalId;
        $this->hash = $hash;

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

    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
        $this->updatedAt = new DateTime();
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getSystemName(): string
    {
        return $this->systemName;
    }

}
