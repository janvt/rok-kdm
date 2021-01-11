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
    const UID_KVK4 = 'kvk4';
    const UID_KVK5 = 'kvk5';

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

    public function __construct()
    {
        $this->governorSnapshots = new ArrayCollection();
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

        return $this;
    }
}
