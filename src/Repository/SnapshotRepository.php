<?php

namespace App\Repository;

use App\Entity\Snapshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Snapshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Snapshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Snapshot[]    findAll()
 * @method Snapshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snapshot::class);
    }

    public function save(Snapshot $snapshot): Snapshot
    {
        $this->_em->persist($snapshot);
        $this->_em->flush();

        return $snapshot;
    }
}
