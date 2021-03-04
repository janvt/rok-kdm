<?php


namespace App\Service\Governor;


use App\Entity\Commander;
use App\Entity\Equipment;
use App\Entity\Governor;
use App\Repository\CommanderRepository;
use App\Repository\EquipmentRepository;

class EquipmentService
{
    private $repo;

    public function __construct(EquipmentRepository $equipmentRepo)
    {
        $this->repo = $equipmentRepo;
    }

    /**
     * @param Governor $gov
     */
    public function ensureAllEquipment(Governor $gov)
    {
        $all = $this->repo->loadAllForGov($gov, false);
        foreach(EquipmentNames::ALL as $uid => $name) {
            if (\array_key_exists($uid, $all)) {
                continue;
            }

            $equipment = new Equipment();
            $equipment->setUid($uid);
            $equipment->setGovernor($gov);

            $this->repo->save($equipment);
        }
    }

    /**
     * @param Governor $gov
     * @return Equipment[]
     */
    public function getAllForGov(Governor $gov): array
    {
        return $this->repo->loadAllForGov($gov);
    }

    public function save(Equipment $equipment): Equipment
    {
        return $this->repo->save($equipment);
    }
}