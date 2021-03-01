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

    /**
     * @param string $searchTerm
     * @return Governor[]
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('g')
            ->select()
            ->leftJoin('g.alliance', 'a')
            ->where('g.name LIKE :searchTerm')
            ->orWhere('a.tag LIKE :searchTerm')
            ->orWhere('a.name LIKE :searchTerm')
            ->orWhere('g.governor_id LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }

    public function save(Governor $gov): Governor
    {
        $this->_em->persist($gov);
        $this->_em->flush();

        return $gov;
    }
}
