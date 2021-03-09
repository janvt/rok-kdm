<?php

namespace App\Entity;

use App\Repository\GovernorProfileClaimRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=GovernorProfileClaimRepository::class)
 */
class GovernorProfileClaim
{
    const PROOF_SEPARATOR = ':';
    const PROOF_TYPE_IMAGE = 'image';

    const STATUS_OPEN = 'open';
    const STATUS_VERIFIED = 'verified';
    const STATUS_CLOSED = 'closed';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="governorProfileClaims")
     * @Ignore()
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $proof;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->status = self::STATUS_OPEN;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProof(): ?string
    {
        return $this->proof;
    }

    public function setProof(string $type, string $id): self
    {
        $this->proof = $type . self::PROOF_SEPARATOR . $id;

        return $this;
    }

    public function isProofType(string $type): bool
    {
        return $this->getProofParts()[0] === $type;
    }

    public function getProofId()
    {
        return $this->getProofParts()[1];
    }

    private function getProofParts(): array
    {
        return explode(self::PROOF_SEPARATOR, $this->getProof());
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
