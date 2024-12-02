<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
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

    #[ORM\Column]
    private ?int $answerId1 = null;

    #[ORM\Column]
    private ?int $answerId2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $answerId3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $answerId4 = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?Quizz $quizz = null;

    #[ORM\Column]
    private ?int $type = 0;

    #[ORM\Column]
    private ?int $position = 0;

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

    public function getAnswerId1(): ?int
    {
        return $this->answerId1;
    }

    public function setAnswerId1(int $answerId1): static
    {
        $this->answerId1 = $answerId1;

        return $this;
    }

    public function getAnswerId2(): ?int
    {
        return $this->answerId2;
    }

    public function setAnswerId2(int $answerId2): static
    {
        $this->answerId2 = $answerId2;

        return $this;
    }

    public function getAnswerId3(): ?int
    {
        return $this->answerId3;
    }

    public function setAnswerId3(?int $answerId3): static
    {
        $this->answerId3 = $answerId3;

        return $this;
    }

    public function getAnswerId4(): ?int
    {
        return $this->answerId4;
    }

    public function setAnswerId4(?int $answerId4): static
    {
        $this->answerId4 = $answerId4;

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
}