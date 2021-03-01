<?php

namespace App\Repository;

use App\Entity\Alliance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alliance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alliance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alliance[]    findAll()
 * @method Alliance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllianceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alliance::class);
    }

    // /**
    //  * @return Alliance[] Returns an array of Alliance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Alliance
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
