<?php

namespace App\Repository;

use App\Entity\Repo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Repo>
 */
class ReadRepoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Repo::class);
    }

    public function exists(string $ghaId): bool
    {
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.ghaId = :ghaId')
            ->setParameter('ghaId', $ghaId)
            ->getQuery();

        return 1 === (int) $query->getScalarResult();
    }
}
