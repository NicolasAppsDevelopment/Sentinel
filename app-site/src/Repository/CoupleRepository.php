<?php

namespace App\Repository;

use App\Entity\Couple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Couple>
 */
class CoupleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Couple::class);
    }

    /**
     * Get all couples for a given user ID.
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findOneByActionDeviceId(int $actionDeviceId): ?Couple
    {
        return $this->createQueryBuilder('c')
            ->where('c.actionDevice = :actionDevice')
            ->setParameter('actionDevice', $actionDeviceId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
