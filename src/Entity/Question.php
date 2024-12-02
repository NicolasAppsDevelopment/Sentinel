<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statement = null;

    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ["persist", 'remove'])]
    private Collection $answers;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?Quizz $quizz = null;

    #[ORM\Column]
    private ?int $type = 0;

    #[ORM\Column]
    private ?int $position = 0;

    #[ORM\OneToMany(targetEntity: QuestionAnswerUserQuizzAttempt::class, mappedBy: 'question', cascade: ["persist", 'remove'])]
    private Collection $userQuizzAttemptAnswers;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer1_id", referencedColumnName: "id", nullable: false)]
    private ?Answer $answer1 = null;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer2_id", referencedColumnName: "id", nullable: false)]
    private ?Answer $answer2 = null;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer3_id", referencedColumnName: "id", nullable: false)]
    private ?Answer $answer3 = null;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer4_id", referencedColumnName: "id", nullable: false)]
    private ?Answer $answer4 = null;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->userQuizzAttemptAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatement(): ?string
    {
        return $this->statement;
    }

    public function setStatement(string $statement): static
    {
        $this->statement = $statement;

        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {

        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    public function getQuizz(): ?Quizz
    {
        return $this->quizz;
    }

    public function setQuizz(?Quizz $quizz): self
    {
        $this->quizz = $quizz;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getUserQuizzAttemptAnswers(): Collection
    {
        return $this->userQuizzAttemptAnswers;
    }

    public function addUserQuizzAttemptAnswer(QuestionAnswerUserQuizzAttempt $userQuizzAttemptAnswer): self
    {

        if (!$this->userQuizzAttemptAnswers->contains($userQuizzAttemptAnswer)) {
            $this->userQuizzAttemptAnswers->add($userQuizzAttemptAnswer);
            $userQuizzAttemptAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeUserQuizzAttemptAnswer(QuestionAnswerUserQuizzAttempt $userQuizzAttemptAnswer): self
    {
        if ($this->userQuizzAttemptAnswers->removeElement($userQuizzAttemptAnswer)) {
            if ($userQuizzAttemptAnswer->getQuestion() === $this) {
                $userQuizzAttemptAnswer->setQuestion(null);
            }
        }

        return $this;
    }

    public function getAnswer1(): ?Answer
    {
        return $this->answer1;
    }

    public function setAnswer1(Answer $answer1): static
    {
        $this->answer1 = $answer1;

        return $this;
    }
}
