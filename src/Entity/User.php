<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'author', cascade: ["persist", 'remove'])]
    private Collection $quizzes;

    #[ORM\OneToMany(targetEntity: UserQuizAttempt::class, mappedBy: 'user', cascade: ["persist", 'remove'], orphanRemoval: true)]
    private Collection $quizAttempts;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\ManyToMany(targetEntity: Quiz::class, inversedBy: 'favoriteOfUsers')]
    private Collection $favoriteQuizzes;


    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->quizAttempts = new ArrayCollection();
        $this->favoriteQuizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getQuizzes (): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setAuthor($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            if ($quiz->getAuthor() === $this) {
                $quiz->setAuthor(null);
            }
        }

        return $this;
    }

    public function getQuizAttempts(): Collection
    {
        return $this->quizAttempts;
    }

    public function addQuizAttempt(UserQuizAttempt $quizAttempt): self
    {
        if (!$this->quizAttempts->contains($quizAttempt)) {
            $this->quizAttempts->add($quizAttempt);
            $quizAttempt->setUser($this);
        }

        return $this;
    }

    public function removeQuizAttempt(UserQuizAttempt $quizAttempt): self
    {
        if ($this->quizAttempts->removeElement($quizAttempt)) {
            if ($quizAttempt->getUser() === $this) {
                $quizAttempt->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getFavoriteQuizzes(): Collection
    {
        return $this->favoriteQuizzes;
    }

    public function addFavoriteQuiz(Quiz $favoriteQuiz): static
    {
        if (!$this->favoriteQuizzes->contains($favoriteQuiz)) {
            $this->favoriteQuizzes->add($favoriteQuiz);
        }

        return $this;
    }

    public function removeFavoriteQuiz(Quiz $favoriteQuiz): static
    {
        $this->favoriteQuizzes->removeElement($favoriteQuiz);

        return $this;
    }

    public function isQuizInFavorite(Quiz $quiz): bool
    {
        return $this->favoriteQuizzes->contains($quiz);
    }
}
