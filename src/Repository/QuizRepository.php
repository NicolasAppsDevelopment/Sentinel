<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('quiz')
            ->andWhere('LOWER(TRIM(quiz.title)) LIKE :title')
            ->setParameter('title', '%' . strtolower(trim($title)) . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTrendQuizzes(): array
    {
        return $this->createQueryBuilder('quiz')
            ->select('quiz, COUNT(usersAttempt) AS HIDDEN userAttemptCount')
            ->leftJoin('quiz.usersAttempts', 'usersAttempt')
            ->groupBy('quiz.id')
            ->orderBy('userAttemptCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getLastQuizzes(): array
    {
        return $this->createQueryBuilder('quiz')
            ->orderBy('quiz.createdDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
