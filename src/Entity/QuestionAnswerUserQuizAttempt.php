<?php

namespace App\Entity;

use App\Repository\QuestionAnswerUserQuizAttemptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\ManyToMany(targetEntity: Answer::class, inversedBy: 'questionAnswerUserQuizAttempts')]
    private Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        $this->answers->removeElement($answer);

        return $this;
    }
}
