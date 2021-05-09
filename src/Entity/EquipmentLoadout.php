<?php

namespace App\Entity;

use App\Repository\EquipmentLoadoutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipmentLoadoutRepository::class)
 */
class EquipmentLoadout
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Governor::class, inversedBy="equipmentLoadouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $governor;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_head;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_head_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_chest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_chest_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_weapon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_weapon_special;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getSlotHead(): ?EquipmentInventory
    {
        return $this->slot_head;
    }

    public function setSlotHead(?EquipmentInventory $slot_head): self
    {
        $this->slot_head = $slot_head;

        return $this;
    }

    public function getSlotHeadSpecial(): ?string
    {
        return $this->slot_head_special;
    }

    public function setSlotHeadSpecial(?string $slot_head_special): self
    {
        $this->slot_head_special = $slot_head_special;

        return $this;
    }

    public function getSlotChest(): ?EquipmentInventory
    {
        return $this->slot_chest;
    }

    public function setSlotChest(?EquipmentInventory $slot_chest): self
    {
        $this->slot_chest = $slot_chest;

        return $this;
    }

    public function getSlotChestSpecial(): ?string
    {
        return $this->slot_chest_special;
    }

    public function setSlotChestSpecial(?string $slot_chest_special): self
    {
        $this->slot_chest_special = $slot_chest_special;

        return $this;
    }

    public function getSlotWeapon(): ?EquipmentInventory
    {
        return $this->slot_weapon;
    }

    public function setSlotWeapon(?EquipmentInventory $slot_weapon): self
    {
        $this->slot_weapon = $slot_weapon;

        return $this;
    }

    public function getSlotWeaponSpecial(): ?string
    {
        return $this->slot_weapon_special;
    }

    public function setSlotWeaponSpecial(?string $slot_weapon_special): self
    {
        $this->slot_weapon_special = $slot_weapon_special;

        return $this;
    }
}
