<?php

namespace App\Entity;

use App\Exception\GovDataException;
use App\Repository\GovernorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=GovernorRepository::class)
 */
class Governor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $governor_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="governors")
     * @Ignore()
     */
    private $user_id;

    /**
     * @ORM\OneToMany(targetEntity=GovernorSnapshot::class, mappedBy="governor_id", orphanRemoval=true)
     */
    private $snapshots;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alliance;

    /**
     * Governor constructor.
     * @param string $id
     * @param string $status
     * @throws GovDataException
     */
    public function __construct(string $id, string $status)
    {
        $this->snapshots = new ArrayCollection();

        $this->id = $id;
        $this->setStatus($status);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGovernorId(): ?string
    {
        return $this->governor_id;
    }

    public function setGovernorId(string $governor_id): self
    {
        $this->governor_id = $governor_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection|GovernorSnapshot[]
     */
    public function getSnapshots(): Collection
    {
        return $this->snapshots;
    }

    public function addSnapshot(GovernorSnapshot $snapshot): self
    {
        if (!$this->snapshots->contains($snapshot)) {
            $this->snapshots[] = $snapshot;
            $snapshot->setGovernorId($this);
        }

        return $this;
    }

    public function removeSnapshot(GovernorSnapshot $snapshot): self
    {
        if ($this->snapshots->removeElement($snapshot)) {
            // set the owning side to null (unless already changed)
            if ($snapshot->getGovernorId() === $this) {
                $snapshot->setGovernorId(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, GovernorStatus::GOV_STATUSES)) {
            throw new GovDataException('Invalid governor status: ' . $status);
        }

        $this->status = $status;

        return $this;
    }

    public function getAlliance(): ?string
    {
        return $this->alliance;
    }

    public function setAlliance(?string $alliance): self
    {
        $this->alliance = $alliance;

        return $this;
    }
}
