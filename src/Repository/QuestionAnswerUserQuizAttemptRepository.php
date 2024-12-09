<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QuestionAnswerUserQuizAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionAnswerUserQuizAttempt>
 */
class QuestionAnswerUserQuizAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionAnswerUserQuizAttempt::class);
    }

    public function getNbOfTimesAnswered(Question $seekQuestion): int
    {
        return (int) $this->createQueryBuilder('questionAnswerUserQuizAttempt')
            ->select('COUNT(questionAnswerUserQuizAttempt.id) AS count')
            ->andWhere('questionAnswerUserQuizAttempt.question = :seekQuestion')
            ->setParameter('seekQuestion', $seekQuestion)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNbOfTimesSelected(int $seekAnswerId): int
    {
        return (int) $this->createQueryBuilder('questionAnswerUserQuizAttempt')
            ->select('COUNT(questionAnswerUserQuizAttempt.id) AS count')
            ->leftJoin('questionAnswerUserQuizAttempt.answers', 'answers')
            ->andWhere('answers.id = :seekAnswerId')
            ->setParameter('seekAnswerId', $seekAnswerId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
