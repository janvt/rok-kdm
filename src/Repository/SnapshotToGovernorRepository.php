<?php

namespace App\Repository;

use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Snapshot;
use App\Entity\SnapshotToGovernor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
     * @return SnapshotToGovernor[]
     */
    public function getMissingGovForSnapshot(Snapshot $snapshot): array
    {
        $query = $this->createQueryBuilder('stg')
            ->select()
            ->leftJoin(
                GovernorSnapshot::class,
                'gs',
                Join::WITH,
                'gs.governor = stg.governor AND gs.snapshot = stg.snapshot'
            )
            ->where('stg.snapshot = :snapshot')
            ->andWhere('gs.id IS NULL')
            ->setParameter('snapshot', $snapshot)
            ->getQuery();

        return $query->getResult();
    }
}
