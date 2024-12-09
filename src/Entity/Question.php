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

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?Quiz $quiz = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ressourceFilename = null;

    #[ORM\Column]
    private ?int $type = 0;

    #[ORM\Column]
    private ?int $position = 0;

    #[ORM\OneToMany(targetEntity: QuestionAnswerUserQuizAttempt::class, mappedBy: 'question', cascade: ["persist", 'remove'])]
    private Collection $userQuizAttemptAnswers;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer1_id", referencedColumnName: "id", nullable: false)]
    private ?Answer $answer1 = null;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer2_id", referencedColumnName: "id", nullable: false)]
    private ?Answer $answer2 = null;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer3_id", referencedColumnName: "id", nullable: true)]
    private ?Answer $answer3 = null;

    #[ORM\OneToOne(targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "answer4_id", referencedColumnName: "id", nullable: true)]
    private ?Answer $answer4 = null;

    public function __construct()
    {
        $this->userQuizAttemptAnswers = new ArrayCollection();
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

    public function getRessourceFilename(): ?string
    {
        return $this->ressourceFilename;
    }

    public function setRessourceFilename(?string $ressourceFilename): static
    {
        $this->ressourceFilename = $ressourceFilename;

        return $this;
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

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getUserQuizAttemptAnswers(): Collection
    {
        return $this->userQuizAttemptAnswers;
    }

    public function addUserQuizAttemptAnswer(QuestionAnswerUserQuizAttempt $userQuizzAttemptAnswer): self
    {

        if (!$this->userQuizAttemptAnswers->contains($userQuizzAttemptAnswer)) {
            $this->userQuizAttemptAnswers->add($userQuizzAttemptAnswer);
            $userQuizzAttemptAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeUserQuizAttemptAnswer(QuestionAnswerUserQuizAttempt $userQuizzAttemptAnswer): self
    {
        if ($this->userQuizAttemptAnswers->removeElement($userQuizzAttemptAnswer)) {
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

    public function getAnswer2(): ?Answer
    {
        return $this->answer2;
    }

    public function setAnswer2(Answer $answer2): static
    {
        $this->answer2 = $answer2;

        return $this;
    }

    public function getAnswer3(): ?Answer
    {
        return $this->answer3;
    }

    public function setAnswer3(?Answer $answer3): static
    {
        $this->answer3 = $answer3;

        return $this;
    }

    public function getAnswer4(): ?Answer
    {
        return $this->answer4;
    }

    public function setAnswer4(?Answer $answer4): static
    {
        $this->answer4 = $answer4;

        return $this;
    }
}
