<?php

namespace App\Entity;

use App\Repository\SnapshotToGovernorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SnapshotToGovernorRepository::class)
 */
class SnapshotToGovernor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Snapshot::class, inversedBy="governors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $snapshot;

    /**
     * @var Governor
     * @ORM\ManyToOne(targetEntity=Governor::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $governor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completed;

    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->governor->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSnapshot(): ?Snapshot
    {
        return $this->snapshot;
    }

    public function setSnapshot(?Snapshot $snapshot): self
    {
        $this->snapshot = $snapshot;

        return $this;
    }

    public function getGovernor(): ?Governor
    {
        return $this->governor;
    }

    public function setGovernor(?Governor $governor): self
    {
        $this->governor = $governor;

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

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

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
