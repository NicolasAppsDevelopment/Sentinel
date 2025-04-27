<?php

namespace App\Repository;

use App\Entity\Couple;
use App\Dto\CoupleDetectionDto;
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

    public function findOneByCameraDeviceId(int $cameraDeviceId): ?Couple
    {
        return $this->createQueryBuilder('c')
            ->where('c.cameraDevice = :cameraDevice')
            ->setParameter('cameraDevice', $cameraDeviceId)
            ->getQuery()
            ->getOneOrNullResult();
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
            ->orderBy('d.triggeredAt')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }


    /**
     * Met à jour la date de dernière consultation pour un seul couple
     */
    public function updateLastDetectionSeekDate(int $coupleId): void
    {
        $qb= $this->createQueryBuilder('c')
            ->update()
            ->set('c.lastDetectionSeekDate', ':now')
            ->where('c.id = :id')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('id', $coupleId)
            ->getQuery()
            ->execute();

            $qb->execute();
            $qb->getEntityManager()->flush();
    }

    public function findCouplesWithNewDetectionCountByUser(int $userId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'COUNT(d.id) AS detectionCount')
            ->leftJoin('c.detections', 'd', 'WITH', 'd.triggeredAt > c.lastDetectionSeekDate')
            ->where('c.user = :userId')
            ->groupBy('c.id')
            ->setParameter('userId', $userId);

            // On récupère les résultats bruts
            $result = $qb->getQuery()->getResult();

            // On transforme les résultats bruts en objets Dto
            $coupleDetectionDto = array_map(function ($row) {
                return new CoupleDetectionDto($row[0], (int) $row['detectionCount']);
            }, $result);

            return $coupleDetectionDto;
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
