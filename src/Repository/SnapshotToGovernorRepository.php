<?php

namespace App\Repository;

use App\Entity\Governor;
use App\Entity\Snapshot;
use App\Entity\SnapshotToGovernor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SnapshotToGovernor|null find($id, $lockMode = null, $lockVersion = null)
 * @method SnapshotToGovernor|null findOneBy(array $criteria, array $orderBy = null)
 * @method SnapshotToGovernor[]    findAll()
 * @method SnapshotToGovernor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnapshotToGovernorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SnapshotToGovernor::class);
    }

    /**
     * @param Snapshot $snapshot
     * @return Governor[]
     */
    public function getMissingGovs(Snapshot $snapshot): array
    {
//        $query = $this->createQueryBuilder('stg')
//            ->select('stg.governor')
//            ->getQuery();
//
//        return $query->getResult();
        return [];
    }
}
