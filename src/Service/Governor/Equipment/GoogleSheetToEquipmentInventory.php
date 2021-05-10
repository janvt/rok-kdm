<?php


namespace App\Service\Governor\Equipment;


use App\Entity\EquipmentInventory;

class GoogleSheetToEquipmentInventory
{
    private $data;

    const MAP = [
        'CavalryAttack' => 5,
        'CavalryDefense' => 6,
        'CavalryHealth' => 7,
        'InfantryAttack' => 8,
        'InfantryDefense' => 9,
        'InfantryHealth' => 10,
        'ArcherAttack' => 11,
        'ArcherDefense' => 12,
        'ArcherHealth' => 13,
        'CavalryMarchSpeed' => 14,
        'InfantryMarchSpeed' => 15,
        'ArcherMarchSpeed' => 16,
        'BarbDamage' => 17,
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getUid()
    {
        return $this->data[0];
    }

    public function map(EquipmentInventory $equipment)
    {
        $len = \count($this->data);

        $equipment->setName($this->data[1]);
        $equipment->setSlot($this->data[2]);

        if ($len > 3 && $this->data[3]) {
            $equipment->setSet($this->data[3]);
        }

        if ($len > 4 && $this->data[4]) {
            $equipment->setTier($this->data[4]);
        }

        foreach (self::MAP as $field => $position) {
            if ($len > $position && $this->data[$position]) {
                $equipment->{'set' . $field}((float) $this->data[$position]);
            } else {
                $equipment->{'set' . $field}(null);
            }
        }
    }
}