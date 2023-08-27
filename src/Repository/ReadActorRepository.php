<?php

namespace App\Repository;

use App\Entity\Actor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actor>
 */
class ReadActorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actor::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function exists(string $ghaId): bool
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.ghaId = :ghaId')
            ->setParameter('ghaId', $ghaId)
            ->getQuery();

        return 1 === $query->getSingleScalarResult();
    }
}
