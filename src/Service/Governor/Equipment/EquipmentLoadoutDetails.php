<?php


namespace App\Service\Governor\Equipment;


use App\Entity\EquipmentInventory;
use App\Entity\EquipmentLoadout;

class EquipmentLoadoutDetails
{
    private $loadout;

    public $id;
    public $name;

    public function __construct(EquipmentLoadout $loadout)
    {
        $this->loadout = $loadout;

        $this->id = $loadout->getId();
        $this->name = $loadout->getName();
    }

    public function getItems(): array
    {
        $items = [];

        foreach (EquipmentInventory::SLOTS as $slot) {
            if ($slotItem = $this->loadout->{'getSlot' . $slot}()) {
                $items[] = $slotItem;
            }
        }

        return $items;
    }
}