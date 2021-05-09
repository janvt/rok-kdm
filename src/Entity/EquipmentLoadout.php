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
    private $slot_helms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_helms_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_weapons;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_weapons_special;

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
    private $slot_gloves;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_gloves_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_legs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_legs_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_boots;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_boots_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_accessories_1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_accessories_1_special;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentInventory::class)
     */
    private $slot_accessories_2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slot_accessories_2_special;

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

    public function getSlotHelms(): ?EquipmentInventory
    {
        return $this->slot_helms;
    }

    public function setSlotHelms(?EquipmentInventory $slot_helms): self
    {
        $this->slot_helms = $slot_helms;

        return $this;
    }

    public function getSlotHelmsSpecial(): ?string
    {
        return $this->slot_helms_special;
    }

    public function setSlotHelmsSpecial(?string $slot_helms_special): self
    {
        $this->slot_helms_special = $slot_helms_special;

        return $this;
    }

    public function getSlotWeapons(): ?EquipmentInventory
    {
        return $this->slot_weapons;
    }

    public function setSlotWeapons(?EquipmentInventory $slot_weapons): self
    {
        $this->slot_weapons = $slot_weapons;

        return $this;
    }

    public function getSlotWeaponsSpecial(): ?string
    {
        return $this->slot_weapons_special;
    }

    public function setSlotWeaponsSpecial(?string $slot_weapons_special): self
    {
        $this->slot_weapons_special = $slot_weapons_special;

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

    public function getSlotGloves(): ?EquipmentInventory
    {
        return $this->slot_gloves;
    }

    public function setSlotGloves(?EquipmentInventory $slot_gloves): self
    {
        $this->slot_gloves = $slot_gloves;

        return $this;
    }

    public function getSlotGlovesSpecial(): ?string
    {
        return $this->slot_gloves_special;
    }

    public function setSlotGlovesSpecial(?string $slot_gloves_special): self
    {
        $this->slot_gloves_special = $slot_gloves_special;

        return $this;
    }

    public function getSlotLegs(): ?EquipmentInventory
    {
        return $this->slot_legs;
    }

    public function setSlotLegs(?EquipmentInventory $slot_legs): self
    {
        $this->slot_legs = $slot_legs;

        return $this;
    }

    public function getSlotLegsSpecial(): ?string
    {
        return $this->slot_legs_special;
    }

    public function setSlotLegsSpecial(?string $slot_legs_special): self
    {
        $this->slot_legs_special = $slot_legs_special;

        return $this;
    }

    public function getSlotBoots(): ?EquipmentInventory
    {
        return $this->slot_boots;
    }

    public function setSlotBoots(?EquipmentInventory $slot_boots): self
    {
        $this->slot_boots = $slot_boots;

        return $this;
    }

    public function getSlotBootsSpecial(): ?string
    {
        return $this->slot_boots_special;
    }

    public function setSlotBootsSpecial(?string $slot_boots_special): self
    {
        $this->slot_boots_special = $slot_boots_special;

        return $this;
    }

    public function getSlotAccessories1(): ?EquipmentInventory
    {
        return $this->slot_accessories_1;
    }

    public function setSlotAccessories1(?EquipmentInventory $slot_accessories_1): self
    {
        $this->slot_accessories_1 = $slot_accessories_1;

        return $this;
    }

    public function getSlotAccessories1Special(): ?string
    {
        return $this->slot_accessories_1_special;
    }

    public function setSlotAccessories1Special(?string $slot_accessories_1_special): self
    {
        $this->slot_accessories_1_special = $slot_accessories_1_special;

        return $this;
    }

    public function getSlotAccessories2(): ?EquipmentInventory
    {
        return $this->slot_accessories_2;
    }

    public function setSlotAccessories2(?EquipmentInventory $slot_accessories_2): self
    {
        $this->slot_accessories_2 = $slot_accessories_2;

        return $this;
    }

    public function getSlotAccessories2Special(): ?string
    {
        return $this->slot_accessories_2_special;
    }

    public function setSlotAccessories2Special(?string $slot_accessories_2_special): self
    {
        $this->slot_accessories_2_special = $slot_accessories_2_special;

        return $this;
    }
}
