<?php

namespace App\Repository;

use App\Entity\EquipmentLoadout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipmentLoadout|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentLoadout|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentLoadout[]    findAll()
 * @method EquipmentLoadout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentLoadoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentLoadout::class);
    }

    public function save(EquipmentLoadout $equipmentLoadout): EquipmentLoadout
    {
        $this->_em->persist($equipmentLoadout);
        $this->_em->flush();

        return $equipmentLoadout;
    }

    public function remove(EquipmentLoadout $loadout)
    {
        $this->_em->remove($loadout);
        $this->_em->flush();
    }
}
