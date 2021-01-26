<?php

namespace App\Repository;

use App\Entity\OfficerNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OfficerNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficerNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficerNote[]    findAll()
 * @method OfficerNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficerNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficerNote::class);
    }

    public function save(OfficerNote $officerNote): OfficerNote
    {
        $this->_em->persist($officerNote);
        $this->_em->flush();

        return $officerNote;
    }
}
