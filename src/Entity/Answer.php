<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column]
    private ?bool $isCorrect = false;

    #[ORM\Column]
    private ?int $numberOfTimesSelected = 0;


    #[ORM\OneToMany(targetEntity: QuestionAnswerUserQuizAttempt::class, mappedBy: 'answer', cascade: ["persist", 'remove'])]
    private Collection $questionsUserQuizAttempt;

    public function __construct()
    {
        $this->questionsUserQuizAttempt = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getNumberOfTimesSelected(): ?int
    {
        return $this->numberOfTimesSelected;
    }

    public function setNumberOfTimesSelected(int $numberOfTimesSelected): static
    {
        $this->numberOfTimesSelected = $numberOfTimesSelected;

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

    public function getQuestionsUserQuizAttempt(): Collection
    {
        return $this->questionsUserQuizAttempt;
    }

    public function addQuestionUserQuizAttempt(QuestionAnswerUserQuizAttempt $questionUserQuizzAttempt): self
    {

        if (!$this->questionsUserQuizAttempt->contains($questionUserQuizzAttempt)) {
            $this->questionsUserQuizAttempt->add($questionUserQuizzAttempt);
            $questionUserQuizzAttempt->setAnswer($this);
        }

        return $this;
    }

    public function removeQuestionUserQuizAttempt(QuestionAnswerUserQuizAttempt $questionUserQuizAttempt): self
    {
        if ($this->questionsUserQuizAttempt->removeElement($questionUserQuizAttempt)) {
            if ($questionUserQuizAttempt->getAnswer() === $this) {
                $questionUserQuizAttempt->setAnswer(null);
            }
        }

        return $this;
    }
}
