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

    // /**
    //  * @return OfficerNote[] Returns an array of OfficerNote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OfficerNote
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
