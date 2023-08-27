<?php

namespace App\Repository;

use App\Entity\Actor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReadActorRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actor::class);
    }

    public function exists(string $ghaId): bool
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.ghaId = :ghaId')
            ->setParameter('ghaId', $ghaId)
            ->getQuery();

        return $query->getScalarResult() === 1;
    }
}
