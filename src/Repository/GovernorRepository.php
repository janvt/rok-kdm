<?php

namespace App\Repository;

use App\Entity\Governor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Governor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Governor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Governor[]    findAll()
 * @method Governor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GovernorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Governor::class);
    }

    // /**
    //  * @return Governor[] Returns an array of Governor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Governor
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
