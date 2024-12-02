<?php

namespace App\Entity;

use App\Repository\QuestionAnswerUserQuizzAttemptRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionAnswerUserQuizzAttemptRepository::class)]
class QuestionAnswerUserQuizzAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $attemptId = null;

    #[ORM\Column]
    private ?int $questionId = null;

    #[ORM\Column]
    private ?int $answerId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttemptId(): ?int
    {
        return $this->attemptId;
    }

    public function setAttemptId(int $attemptId): static
    {
        $this->attemptId = $attemptId;

        return $this;
    }

    public function getQuestionId(): ?int
    {
        return $this->questionId;
    }

    public function setQuestionId(int $questionId): static
    {
        $this->questionId = $questionId;

        return $this;
    }

    public function getAnswerId(): ?int
    {
        return $this->answerId;
    }

    public function setAnswerId(int $answerId): static
    {
        $this->answerId = $answerId;

        return $this;
    }
}
