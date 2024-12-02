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
    private ?bool $isCorrect = null;

    #[ORM\Column]
    private ?int $numberOfTimesSelected = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private ?Question $question = null;

    #[ORM\OneToMany(targetEntity: QuestionAnswerUserQuizzAttempt::class, mappedBy: 'answer', cascade: ["persist", 'remove'])]
    private Collection $questionsUserQuizzAttempt;

    public function __construct()
    {
        $this->questionsUserQuizzAttempt = new ArrayCollection();
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

    public function setCorrect(bool $isCorrect): static
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

    public function getQuestionsUserQuizzAttempt(): Collection
    {
        return $this->questionsUserQuizzAttempt;
    }

    public function addQuestionUserQuizzAttempt(QuestionAnswerUserQuizzAttempt $questionUserQuizzAttempt): self
    {

        if (!$this->questionsUserQuizzAttempt->contains($questionUserQuizzAttempt)) {
            $this->questionsUserQuizzAttempt->add($questionUserQuizzAttempt);
            $questionUserQuizzAttempt->setAnswer($this);
        }

        return $this;
    }

    public function removeQuestionUserQuizzAttempt(QuestionAnswerUserQuizzAttempt $questionUserQuizzAttempt): self
    {
        if ($this->questionsUserQuizzAttempt->removeElement($questionUserQuizzAttempt)) {
            if ($questionUserQuizzAttempt->getAnswer() === $this) {
                $questionUserQuizzAttempt->setAnswer(null);
            }
        }

        return $this;
    }
}
