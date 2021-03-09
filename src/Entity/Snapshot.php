<?php

namespace App\Entity;

use App\Repository\SnapshotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=SnapshotRepository::class)
 */
class Snapshot
{
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uid;

    /**
     * @ORM\OneToMany(targetEntity=GovernorSnapshot::class, mappedBy="snapshot")
     * @Ignore()
     */
    private $governorSnapshots;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completed;

    /**
     * @ORM\OneToMany(targetEntity=SnapshotToGovernor::class, mappedBy="snapshot", orphanRemoval=true)
     */
    private $governors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->governorSnapshots = new ArrayCollection();
        $this->governors = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name . ' (' . $this->uid . ')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return Collection|GovernorSnapshot[]
     */
    public function getGovernorSnapshots(): Collection
    {
        return $this->governorSnapshots;
    }

    public function addGovernorSnapshot(GovernorSnapshot $governorSnapshot): self
    {
        if (!$this->governorSnapshots->contains($governorSnapshot)) {
            $this->governorSnapshots[] = $governorSnapshot;
            $governorSnapshot->setSnapshot($this);
        }

        return $this;
    }

    public function removeGovernorSnapshot(GovernorSnapshot $governorSnapshot): self
    {
        if ($this->governorSnapshots->removeElement($governorSnapshot)) {
            // set the owning side to null (unless already changed)
            if ($governorSnapshot->getSnapshot() === $this) {
                $governorSnapshot->setSnapshot(null);
            }
        }

        return $this;
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

    public function getCompleted(): ?\DateTimeInterface
    {
        return $this->completed;
    }

    public function setCompleted(?\DateTimeInterface $completed): self
    {
        $this->completed = $completed;
        $this->status = self::STATUS_COMPLETED;

        return $this;
    }

    /**
     * @return Collection|SnapshotToGovernor[]
     */
    public function getGovernors(): Collection
    {
        return $this->governors;
    }

    public function addGovernor(SnapshotToGovernor $governor): self
    {
        if (!$this->governors->contains($governor)) {
            $this->governors[] = $governor;
            $governor->setSnapshot($this);
        }

        return $this;
    }

    public function removeGovernor(SnapshotToGovernor $governor): self
    {
        if ($this->governors->removeElement($governor)) {
            // set the owning side to null (unless already changed)
            if ($governor->getSnapshot() === $this) {
                $governor->setSnapshot(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
