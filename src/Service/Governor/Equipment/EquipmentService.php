<?php


namespace App\Service\Governor\Equipment;


use App\Entity\Equipment;
use App\Entity\EquipmentInventory;
use App\Entity\EquipmentLoadout;
use App\Entity\Governor;
use App\Exception\NotFoundException;
use App\Repository\EquipmentInventoryRepository;
use App\Repository\EquipmentLoadoutRepository;
use App\Repository\EquipmentRepository;

class EquipmentService
{
    private $repo;
    private $loadoutRepo;
    private $inventoryRepo;

    public function __construct(
        EquipmentRepository $equipmentRepo,
        EquipmentLoadoutRepository $loadoutRepo,
        EquipmentInventoryRepository $inventoryRepo
    )
    {
        $this->repo = $equipmentRepo;
        $this->loadoutRepo = $loadoutRepo;
        $this->inventoryRepo = $inventoryRepo;
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

    public function saveEquipment(Equipment $equipment): Equipment
    {
        return $this->repo->save($equipment);
    }

    public function addLoadout(Governor $gov): EquipmentLoadout
    {
        $loadout = new EquipmentLoadout();
        $loadout->setGovernor($gov);
        $loadout->setName('Set');

        return $this->loadoutRepo->save($loadout);
    }

    public function getLoadout(int $loadoutId): EquipmentLoadout
    {
        $loadout = $this->loadoutRepo->find($loadoutId);

        if (!$loadout) {
            throw new NotFoundException('loadout', $loadoutId);
        }

        return $loadout;
    }

    public function saveLoadout(EquipmentLoadout $loadout): EquipmentLoadout
    {
        return $this->loadoutRepo->save($loadout);
    }

    /**
     * @param Governor $gov
     * @return EquipmentLoadoutDetails[]
     */
    public function getLoadouts(Governor $gov): array
    {
        $loadoutDetails = [];
        $loadouts = $this->loadoutRepo->findBy(['governor' => $gov]);
        foreach ($loadouts as $loadout) {
            $loadoutDetails[] = new EquipmentLoadoutDetails($loadout);
        }

        return $loadoutDetails;
    }

    public function deleteLoadout(EquipmentLoadout $loadout)
    {
        $this->loadoutRepo->remove($loadout);
    }

    public function importInventory(array $values): EquipmentInventoryImportResult
    {
        $result = new EquipmentInventoryImportResult();

        foreach ($values as $data) {
            if (\count($data) < 3) {
                $result->addInvalidRow();
                continue;
            }

            $gsMap = new GoogleSheetToEquipmentInventory($data);

            $equipment = $this->inventoryRepo->findOneBy(['uid' => $gsMap->getUid()]);
            if (!$equipment) {
                $equipment = new EquipmentInventory();
                $equipment->setUid($data[0]);
            }

            $gsMap->map($equipment);

            $this->inventoryRepo->save($equipment);

            $result->addRowImported();
        }

        return $result;
    }
}