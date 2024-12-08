<?php

namespace App\Entity;

use App\Repository\UserQuizAttemptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserQuizAttemptRepository::class)]
class UserQuizAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'usersAttempts')]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'quizAttempt')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column]
    private ?bool $finished = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $playedDate = null;

    #[ORM\OneToMany(targetEntity: QuestionAnswerUserQuizAttempt::class, mappedBy: 'attempt', cascade: ["persist", 'remove'])]
    private Collection $questionAnswers;

    public function __construct()
    {
        $this->questionAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): static
    {
        $this->finished = $finished;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getPlayedDate(): ?\DateTimeInterface
    {
        return $this->playedDate;
    }

    public function setPlayedDate(\DateTimeInterface $playedDate): static
    {
        $this->playedDate = $playedDate;

        return $this;
    }


    public function getQuestionAnswers(): Collection
    {
        return $this->questionAnswers;
    }

    public function addQuestionAnswers(QuestionAnswerUserQuizAttempt $questionAnswer): self
    {

        if (!$this->questionAnswers->contains($questionAnswer)) {
            $this->questionAnswers->add($questionAnswer);
            $questionAnswer->setAttempt($this);
        }

        return $this;
    }

    public function removeQuestionAnswers(QuestionAnswerUserQuizAttempt $questionAnswer): self
    {
        if ($this->questionAnswers->removeElement($questionAnswer)) {
            if ($questionAnswer->getAttempt() === $this) {
                $questionAnswer->setAttempt(null);
            }
        }

        return $this;
    }
}
