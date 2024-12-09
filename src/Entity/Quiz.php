<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $illustrationFilename = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    private ?User $author = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', cascade: ["persist", 'remove'])]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: UserQuizAttempt::class, mappedBy: 'quiz', cascade: ["persist", 'remove'])]
    private Collection $usersAttempts;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteQuizzes')]
    private Collection $favoriteOfUsers;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->usersAttempts = new ArrayCollection();
        $this->favoriteOfUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIllustrationFilename(): ?string
    {
        return $this->illustrationFilename;
    }

    public function setIllustrationFilename(?string $illustrationFilename): void
    {
        $this->illustrationFilename = $illustrationFilename;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): static
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getQuestions(): Collection
    {
        // order questions by position
        $criteria = Criteria::create()->orderBy(['position' => 'ASC']);
        return $this->questions->matching($criteria);
    }

    public function getNextQuestion(Question $question): ?Question
    {
        $questions = $this->getQuestions();
        $nextQuestion = null;
        $next = false;
        foreach ($questions as $q) {
            if ($next) {
                $nextQuestion = $q;
                break;
            }
            if ($q === $question) {
                $next = true;
            }
        }
        return $nextQuestion;
    }

    public function addQuestion(Question $question): self
    {

        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    public function getUsersAttempts(): Collection
    {
        return $this->usersAttempts;
    }

    public function addUserAttempt(UserQuizAttempt $userAttempt): self
    {

        if (!$this->usersAttempts->contains($userAttempt)) {
            $this->usersAttempts->add($userAttempt);
            $userAttempt->setQuiz($this);
        }

        return $this;
    }

    public function removeUserAttempt(UserQuizAttempt $userAttempt): self
    {
        if ($this->usersAttempts->removeElement($userAttempt)) {
            if ($userAttempt->getQuiz() === $this) {
                $userAttempt->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoriteOfUsers(): Collection
    {
        return $this->favoriteOfUsers;
    }

    public function addFavoriteOfUser(User $favoriteOfUser): static
    {
        if (!$this->favoriteOfUsers->contains($favoriteOfUser)) {
            $this->favoriteOfUsers->add($favoriteOfUser);
            $favoriteOfUser->addFavoriteQuiz($this);
        }

        return $this;
    }

    public function removeFavoriteOfUser(User $favoriteOfUser): static
    {
        if ($this->favoriteOfUsers->removeElement($favoriteOfUser)) {
            $favoriteOfUser->removeFavoriteQuiz($this);
        }

        return $this;
    }

}
