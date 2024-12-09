<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\User;
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

    public function findByTitleInMyQuizzes(string $title, User $author): array
    {
        return $this->createQueryBuilder('quiz')
            ->andWhere('LOWER(TRIM(quiz.title)) LIKE :title')
            ->setParameter('title', '%' . strtolower(trim($title)) . '%')
            ->andWhere('quiz.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->getResult();
    }

    public function findByTitleInMyLiked(string $title, User $user)
    {
        return $this->createQueryBuilder('quiz')
            ->andWhere('LOWER(TRIM(quiz.title)) LIKE :title')
            ->setParameter('title', '%' . strtolower(trim($title)) . '%')
            ->andWhere(':author IN (:users)')
            ->setParameter('author', $user)
            ->setParameter('users', $user->getLikedQuizzes())
            ->getQuery()
            ->getResult();
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

    public function getNbOfTimesPlayed(int $quizzId): int
    {
        return (int) $this->createQueryBuilder('quiz')
            ->select('COUNT(usersAttempt) AS count')
            ->leftJoin('quiz.usersAttempts', 'usersAttempt')
            ->andWhere('quiz.id = :id')
            ->setParameter('id', $quizzId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMostLikedQuizzes(): array
    {
        return $this->createQueryBuilder('quiz')
            ->select('quiz, COUNT(user) AS HIDDEN userCount')
            ->leftJoin('quiz.usersLiked', 'user')
            ->groupBy('quiz.id')
            ->orderBy('userCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
