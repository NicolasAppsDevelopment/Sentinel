<?php

namespace App\Repository;

use App\Entity\UserQuizAttempt;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserQuizAttempt>
 */
class UserQuizAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserQuizAttempt::class);
    }

    //    /**
    //     * @return UserQuizzAttempt[] Returns an array of UserQuizzAttempt objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserQuizzAttempt
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getUserNbQuizzesPlayed(User $user): array
    {
        return $this->createQueryBuilder('userQuizzAttempt')
            ->andWhere('userQuizzAttempt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getUserNbQuestionsAnswered(User $user): array
    {
        return $this->createQueryBuilder('userQuizzAttempt')
            ->select('questionAnswers.id')
            ->leftJoin('userQuizzAttempt.questionAnswers', 'questionAnswers')
            ->where('userQuizzAttempt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getUserNbQuestionsAnsweredCorrectly(User $user): array
    {
        return $this->createQueryBuilder('userQuizzAttempt')
            ->select('questionAnswers.id')
            ->leftJoin('userQuizzAttempt.questionAnswers', 'questionAnswers')
            ->leftJoin('questionAnswers.answer', 'answer')
            ->where('userQuizzAttempt.user = :user')
            ->setParameter('user', $user)
            ->andWhere('answer.isCorrect = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();
    }

    public function getUserLatestAttempt(User $user): array
    {
        return $this->createQueryBuilder('userQuizzAttempt')
            ->andWhere('userQuizzAttempt.user = :user')
            ->setParameter('user', $user)
            ->orderBy('userQuizzAttempt.playedDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }
}
