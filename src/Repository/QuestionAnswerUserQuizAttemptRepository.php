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

    public function getPercentageOfTimesSelected(Question $question, ?int $answerId): int
    {
        if ($answerId === null) {
            return 0;
        }

        $total = $this->getNbOfTimesAnswered($question);
        $selected = $this->getNbOfTimesSelected($answerId);
        return $total === 0 ? 0 : round(($selected / $total) * 100, 0, PHP_ROUND_HALF_UP);
    }
}
