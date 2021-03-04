<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Governor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipment[]    findAll()
 * @method Equipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    public function save(Equipment $equipment): Equipment
    {
        $this->_em->persist($equipment);
        $this->_em->flush();

        return $equipment;
    }

    /**
     * @param Governor $gov
     * @param bool $onlyCrafted
     * @return Equipment[]
     */
    public function loadAllForGov(Governor $gov, bool $onlyCrafted = true): array
    {
        $criteria = ['governor' => $gov];

        if  ($onlyCrafted) {
            $criteria['crafted'] = true;
        }

        $all = $this->findBy($criteria);
        $allByUid = [];
        foreach($all as $item) {
            $allByUid[$item->getUid()] = $item;
        }

        return $allByUid;
    }
}
