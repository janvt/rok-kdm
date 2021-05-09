<?php

namespace App\Repository;

use App\Entity\EquipmentInventory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipmentInventory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentInventory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentInventory[]    findAll()
 * @method EquipmentInventory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentInventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentInventory::class);
    }

    public function save(EquipmentInventory $equipment): EquipmentInventory
    {
        $this->_em->persist($equipment);
        $this->_em->flush();

        return $equipment;
    }
}
