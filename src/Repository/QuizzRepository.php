<?php

namespace App\Repository;

use App\Entity\Quizz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quizz>
 */
class QuizzRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quizz::class);
    }

    //    /**
    //     * @return Quizz[] Returns an array of Quizz objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('quizz')
            ->andWhere('quizz.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTrendQuizzes(): array
    {
        return $this->createQueryBuilder('quizz')
            ->andWhere('quizz.title = :title')
            ->setParameter('title', 10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLastQuizzes(): array
    {
        return $this->createQueryBuilder('quizz')
            ->orderBy('quizz.createdDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
}
