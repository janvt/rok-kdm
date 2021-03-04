<?php

namespace App\Repository;

use App\Entity\Commander;
use App\Entity\Governor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commander|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commander|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commander[]    findAll()
 * @method Commander[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommanderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commander::class);
    }

    /**
     * @param Governor $gov
     * @return Commander[]
     */
    public function loadAllForGov(Governor $gov): array
    {
        $all = $this->findBy(['governor' => $gov], ['uid' => 'ASC']);
        $allByUid = [];
        foreach ($all as $commander) {
            $allByUid[$commander->getUid()] = $commander;
        }

        return $allByUid;
    }

    public function save(Commander $commander): Commander
    {
        $this->_em->persist($commander);
        $this->_em->flush();

        return $commander;
    }
}
