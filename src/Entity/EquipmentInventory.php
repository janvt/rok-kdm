<?php

namespace App\Entity;

use App\Repository\EquipmentInventoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipmentInventoryRepository::class)
 */
class EquipmentInventory
{
    const UID_SPECIAL_TALENT_LEADERSHIP = 'st_leadership';
    const UID_SPECIAL_TALENT_INFANTRY = 'st_infantry';
    const UID_SPECIAL_TALENT_CAVALRY = 'st_cavalry';
    const UID_SPECIAL_TALENT_ARCHER = 'st_archer';
    const UID_SPECIAL_TALENT_INTEGRATION = 'st_integration';

    const SPECIAL_TALENT_CHOICES = [
        'Leadership' => self::UID_SPECIAL_TALENT_LEADERSHIP,
        'Cavalry' => self::UID_SPECIAL_TALENT_CAVALRY,
        'Infantry' => self::UID_SPECIAL_TALENT_INFANTRY,
        'Archer' => self::UID_SPECIAL_TALENT_ARCHER,
        'Integration' => self::UID_SPECIAL_TALENT_INTEGRATION,
    ];

    const SLOTS = [
        'Helms',
        'Weapons',
        'Chest',
        'Gloves',
        'Legs',
        'Boots',
        'Accessories1',
        'Accessories2',
    ];

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cavalry_attack;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cavalry_defense;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cavalry_health;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $infantry_attack;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $infantry_defense;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $infantry_health;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $archer_attack;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $archer_defense;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $archer_health;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slot;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $set;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cavalry_march_speed;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $infantry_march_speed;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $archer_march_speed;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $barb_damage;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCavalryAttack(): ?float
    {
        return $this->cavalry_attack;
    }

    public function setCavalryAttack(?float $cavalry_attack): self
    {
        $this->cavalry_attack = $cavalry_attack;

        return $this;
    }

    public function getCavalryDefense(): ?float
    {
        return $this->cavalry_defense;
    }

    public function setCavalryDefense(?float $cavalry_defense): self
    {
        $this->cavalry_defense = $cavalry_defense;

        return $this;
    }

    public function getCavalryHealth(): ?float
    {
        return $this->cavalry_health;
    }

    public function setCavalryHealth(?float $cavalry_health): self
    {
        $this->cavalry_health = $cavalry_health;

        return $this;
    }

    public function getInfantryAttack(): ?float
    {
        return $this->infantry_attack;
    }

    public function setInfantryAttack(?float $infantry_attack): self
    {
        $this->infantry_attack = $infantry_attack;

        return $this;
    }

    public function getInfantryDefense(): ?float
    {
        return $this->infantry_defense;
    }

    public function setInfantryDefense(?float $infantry_defense): self
    {
        $this->infantry_defense = $infantry_defense;

        return $this;
    }

    public function getInfantryHealth(): ?float
    {
        return $this->infantry_health;
    }

    public function setInfantryHealth(?float $infantry_health): self
    {
        $this->infantry_health = $infantry_health;

        return $this;
    }

    public function getArcherAttack(): ?float
    {
        return $this->archer_attack;
    }

    public function setArcherAttack(?float $archer_attack): self
    {
        $this->archer_attack = $archer_attack;

        return $this;
    }

    public function getArcherDefense(): ?float
    {
        return $this->archer_defense;
    }

    public function setArcherDefense(?float $archer_defense): self
    {
        $this->archer_defense = $archer_defense;

        return $this;
    }

    public function getArcherHealth(): ?float
    {
        return $this->archer_health;
    }

    public function setArcherHealth(?float $archer_health): self
    {
        $this->archer_health = $archer_health;

        return $this;
    }

    public function getSlot(): ?string
    {
        return $this->slot;
    }

    public function setSlot(string $slot): self
    {
        $this->slot = $slot;

        return $this;
    }

    public function getSet(): ?string
    {
        return $this->set;
    }

    public function setSet(?string $set): self
    {
        $this->set = $set;

        return $this;
    }

    public function getCavalryMarchSpeed(): ?float
    {
        return $this->cavalry_march_speed;
    }

    public function setCavalryMarchSpeed(?float $cavalry_march_speed): self
    {
        $this->cavalry_march_speed = $cavalry_march_speed;

        return $this;
    }

    public function getInfantryMarchSpeed(): ?float
    {
        return $this->infantry_march_speed;
    }

    public function setInfantryMarchSpeed(?float $infantry_march_speed): self
    {
        $this->infantry_march_speed = $infantry_march_speed;

        return $this;
    }

    public function getArcherMarchSpeed(): ?float
    {
        return $this->archer_march_speed;
    }

    public function setArcherMarchSpeed(?float $archer_march_speed): self
    {
        $this->archer_march_speed = $archer_march_speed;

        return $this;
    }

    public function getBarbDamage(): ?float
    {
        return $this->barb_damage;
    }

    public function setBarbDamage(?float $barb_damage): self
    {
        $this->barb_damage = $barb_damage;

        return $this;
    }
}
