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



    /**
     * Récupère les couples ayant au moins une détection pour un utilisateur donné.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     * @return Couple[] La liste des couples avec leurs détections.
     */
    public function findCouplesWithDetectionsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.detections', 'd')
            ->where('c.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('d.triggered_at')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }





//    /**
//     * @return Couple[] Returns an array of Couple objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Couple
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
