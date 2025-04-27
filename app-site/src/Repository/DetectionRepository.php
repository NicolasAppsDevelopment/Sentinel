<?php

namespace App\Repository;

use App\Entity\Detection;
use App\Entity\Couple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Detection>
 */
class DetectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Detection::class);
    }

    public function findByCoupleId(int $coupleId): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.couple = :couple')
            ->setParameter('couple', $coupleId)
            ->orderBy('d.triggeredAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les détections après une certaine date pour un couple donné
     */
    public function findNewDetectionsSince(int $coupleId, \DateTimeInterface $since): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.couple = :coupleId')
            ->andWhere('d.triggeredAt > :since')
            ->setParameter('coupleId', $coupleId)
            ->setParameter('since', $since)
            ->getQuery()
            ->getResult();
    }




//    /**
//     * @return Detections[] Returns an array of Detections objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Detections
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByUserId(int $userId)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.couple', 'c')
            ->where('c.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('d.triggeredAt', 'DESC')
            ->getQuery();
    }
}
