<?php

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImportRepository::class)
 */
class Import
{
    const STATUS_CONFIGURING = 'CONFIGURING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELED = 'CANCELED';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=65535, nullable=true)
     */
    private $input;

    /**
     * @ORM\Column(type="string", length=65535, nullable=true)
     */
    private $mappings;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Snapshot::class)
     */
    private $snapshot;

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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getMappings(): ?string
    {
        return $this->mappings;
    }

    public function setMappings(?string $mappings): self
    {
        $this->mappings = $mappings;

        return $this;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function setInput(?string $input): self
    {
        $this->input = $input;

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
