<?php

namespace App\Repository;

use App\Entity\Alliance;
use App\Entity\Governor;
use App\Entity\GovernorStatus;
use App\Service\Export\ExportFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;
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

    /**
     * @return Governor[]
     */
    public function getGovernorsFromMainAlliances(): array
    {
        return $this->createQueryBuilder('g')
            ->select()
            ->join('g.alliance', 'a')
            ->where('a.type = :type')
            ->setParameter('type', Alliance::TYPE_MAIN)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $commander1
     * @param string|null $commander2
     * @return Governor[]
     */
    public function searchCommanders(string $commander1, ?string $commander2): array
    {
        $queryBuilder = $this->createQueryBuilder('g')
            ->join('g.commanders', 'c')
            ->where('c.uid = :commander1')
            ->andWhere('c.skills = :skills')
            ->andWhere('g.status = :status')
            ->setParameter('commander1', $commander1)
            ->setParameter('skills', '5555')
            ->setParameter('status', GovernorStatus::STATUS_ACTIVE)
        ;

        if ($commander2) {
            $queryBuilder
                ->join('g.commanders', 'c2')
                ->andWhere('c2.uid = :commander2')
                ->andWhere('c2.skills = :skills')
                ->setParameter('commander2', $commander2)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param ExportFilter $filter
     * @return IterableResult
     */
    public function getGovIterator(ExportFilter $filter): IterableResult
    {
        $queryBuilder = $this->createQueryBuilder('g')
            ->select()
            ->leftJoin('g.alliance', 'a')
            ->orderBy('g.name');

        if ($filter->getAlliance()) {
            $queryBuilder
                ->andWhere('g.alliance = :alliance')
                ->setParameter('alliance', $filter->getAlliance());
        }

        if ($filter->getGovStatus()) {
            $queryBuilder
                ->andWhere('g.status = :status')
                ->setParameter('status', $filter->getGovStatus());
        }

        return $queryBuilder->getQuery()->iterate();
    }
}
