<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipmentRepository::class)
 */
class Equipment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uid;

    /**
     * @ORM\ManyToOne(targetEntity=Governor::class, inversedBy="equipment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $governor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $specialTalent;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $crafted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $blueprint;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGovernor(): ?Governor
    {
        return $this->governor;
    }

    public function setGovernor(?Governor $governor): self
    {
        $this->governor = $governor;

        return $this;
    }

    public function getSpecialTalent(): ?string
    {
        return $this->specialTalent;
    }

    public function setSpecialTalent(?string $specialTalent): self
    {
        $this->specialTalent = $specialTalent;

        return $this;
    }

    public function getCrafted(): ?bool
    {
        return $this->crafted;
    }

    public function setCrafted(?bool $crafted): self
    {
        $this->crafted = $crafted;

        return $this;
    }

    public function getBlueprint(): ?bool
    {
        return $this->blueprint;
    }

    public function setBlueprint(?bool $blueprint): self
    {
        $this->blueprint = $blueprint;

        return $this;
    }
}
