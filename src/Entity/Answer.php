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

    /**
     * @var Collection<int, QuestionAnswerUserQuizAttempt>
     */
    #[ORM\ManyToMany(targetEntity: QuestionAnswerUserQuizAttempt::class, mappedBy: 'answers')]
    private Collection $questionAnswerUserQuizAttempts;

    public function __construct()
    {
        $this->questionAnswerUserQuizAttempts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
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

    /**
     * @return Collection<int, QuestionAnswerUserQuizAttempt>
     */
    public function getQuestionAnswerUserQuizAttempts(): Collection
    {
        return $this->questionAnswerUserQuizAttempts;
    }

    public function addQuestionAnswerUserQuizAttempt(QuestionAnswerUserQuizAttempt $questionAnswerUserQuizAttempt): static
    {
        if (!$this->questionAnswerUserQuizAttempts->contains($questionAnswerUserQuizAttempt)) {
            $this->questionAnswerUserQuizAttempts->add($questionAnswerUserQuizAttempt);
            $questionAnswerUserQuizAttempt->addAnswer($this);
        }

        return $this;
    }

    public function removeQuestionAnswerUserQuizAttempt(QuestionAnswerUserQuizAttempt $questionAnswerUserQuizAttempt): static
    {
        if ($this->questionAnswerUserQuizAttempts->removeElement($questionAnswerUserQuizAttempt)) {
            $questionAnswerUserQuizAttempt->removeAnswer($this);
        }

        return $this;
    }
}
