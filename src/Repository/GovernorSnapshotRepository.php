<?php

namespace App\Repository;

use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Snapshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GovernorSnapshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method GovernorSnapshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method GovernorSnapshot[]    findAll()
 * @method GovernorSnapshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GovernorSnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GovernorSnapshot::class);
    }

    public function getGovSnapshotForSnapshot(Governor $gov, Snapshot $snapshot): ?GovernorSnapshot
    {
        return $this->findOneBy([
            'governor' => $gov,
            'snapshot' => $snapshot
        ]);
    }

    /**
     * @param GovernorSnapshot $snapshot
     * @return GovernorSnapshot
     */
    public function save(GovernorSnapshot $snapshot): GovernorSnapshot
    {
        $this->_em->persist($snapshot);
        $this->_em->flush();

        return $snapshot;
    }
}
