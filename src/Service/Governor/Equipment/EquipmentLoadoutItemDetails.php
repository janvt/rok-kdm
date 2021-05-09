<?php


namespace App\Service\Governor\Equipment;


use App\Entity\EquipmentInventory;

class EquipmentLoadoutItemDetails
{
    private $equipment;
    private $specialTalentNames;
    private $specialTalent;

    public function __construct(EquipmentInventory $equipment, ?string $specialTalent)
    {
        $this->equipment = $equipment;
        $this->specialTalent = $specialTalent;

        $this->specialTalentNames = \array_flip(EquipmentInventory::SPECIAL_TALENT_CHOICES);
    }

    public function getName(): string
    {
        return $this->equipment->getName();
    }

    public function getSpecialTalent(): ?string
    {
        if ($this->specialTalent) {
            return $this->specialTalentNames[$this->specialTalent] . ' Special Talent';
        }

        return null;
    }
}