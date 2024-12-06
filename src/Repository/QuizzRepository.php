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

    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('quizz')
            ->andWhere('LOWER(TRIM(quizz.title)) LIKE :title')
            ->setParameter('title', '%' . strtolower(trim($title)) . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTrendQuizzes(): array
    {
        return $this->createQueryBuilder('quizz')
            ->select('quizz, COUNT(usersAttempt) AS HIDDEN userAttemptCount')
            ->leftJoin('quizz.usersAttempts', 'usersAttempt')
            ->groupBy('quizz.id')
            ->orderBy('userAttemptCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getLastQuizzes(): array
    {
        return $this->createQueryBuilder('quizz')
            ->orderBy('quizz.createdDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
