<?php

namespace App\Entity;

use App\Repository\AllianceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AllianceRepository::class)
 */
class Alliance
{
    const TYPE_MAIN = 'MAIN';
    const TYPE_FARM = 'FARM';
    const TYPE_SHELL = 'SHELL';
    const TYPE_UNSANCTIONED = 'UNSANCTIONED';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    public function __toString(): string
    {
        return $this->getTag();
    }

    public function getDisplayName(): string
    {
        return '[' . $this->getTag() . '] ' . $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
