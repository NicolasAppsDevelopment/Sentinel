<?php

namespace App\Entity;

use App\Repository\QuestionAnswerUserQuizAttemptRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionAnswerUserQuizAttemptRepository::class)]
class QuestionAnswerUserQuizAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questionAnswers')]
    private ?UserQuizAttempt $attempt = null;

    #[ORM\ManyToOne(inversedBy: 'userQuizzAttemptAnswers')]
    private ?Question $question = null;

    #[ORM\ManyToOne(inversedBy: 'questionsUserQuizzAttempt')]
    private ?Answer $answer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttempt(): ?UserQuizAttempt
    {
        return $this->attempt;
    }

    public function setAttempt(?UserQuizAttempt $attempt): self
    {
        $this->attempt = $attempt;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
