<?php


namespace App\Service\Governor\Equipment;


use App\Entity\EquipmentInventory;

class GoogleSheetToEquipmentInventory
{
    private $data;

    const MAP = [
        'CavalryAttack' => 4,
        'CavalryDefense' => 5,
        'CavalryHealth' => 6,
        'InfantryAttack' => 7,
        'InfantryDefense' => 8,
        'InfantryHealth' => 9,
        'ArcherAttack' => 10,
        'ArcherDefense' => 11,
        'ArcherHealth' => 12,
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

        foreach (self::MAP as $field => $position) {
            if ($len > $position && $this->data[$position]) {
                $equipment->{'set' . $field}((float) $this->data[$position]);
            } else {
                $equipment->{'set' . $field}(null);
            }
        }
    }
}