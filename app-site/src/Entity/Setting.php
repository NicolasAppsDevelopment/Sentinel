<?php

namespace App\Entity;

use App\Repository\CoupleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoupleRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\LessThanOrEqual(propertyPath: "mondayTo", message: "The monday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $mondayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "mondayFrom", message: "The monday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $mondayTo = null;

    #[Assert\LessThanOrEqual(propertyPath: "tuesdayTo", message: "The tuesday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $tuesdayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "tuesdayFrom", message: "The tuesday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $tuesdayTo = null;

    #[Assert\LessThanOrEqual(propertyPath: "wednesdayTo", message: "The wednesday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $wednesdayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "wednesdayFrom", message: "The wednesday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $wednesdayTo = null;

    #[Assert\LessThanOrEqual(propertyPath: "thursdayTo", message: "The thursday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $thursdayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "thursdayFrom", message: "The thursday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $thursdayTo = null;

    #[Assert\LessThanOrEqual(propertyPath: "fridayTo", message: "The friday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $fridayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "fridayFrom", message: "The friday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $fridayTo = null;

    #[Assert\LessThanOrEqual(propertyPath: "saturdayTo", message: "The saturday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $saturdayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "saturdayFrom", message: "The saturday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $saturdayTo = null;

    #[Assert\LessThanOrEqual(propertyPath: "sundayTo", message: "The sunday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $sundayFrom = null;

    #[Assert\GreaterThanOrEqual(propertyPath: "sundayFrom", message: "The sunday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $sundayTo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getMondayFrom(): ?\DateTimeInterface
    {
        return $this->mondayFrom;
    }

    public function setMondayFrom(?\DateTimeInterface $mondayFrom): void
    {
        $this->mondayFrom = $mondayFrom;
    }

    public function getMondayTo(): ?\DateTimeInterface
    {
        return $this->mondayTo;
    }

    public function setMondayTo(?\DateTimeInterface $mondayTo): void
    {
        $this->mondayTo = $mondayTo;
    }

    public function getTuesdayFrom(): ?\DateTimeInterface
    {
        return $this->tuesdayFrom;
    }

    public function setTuesdayFrom(?\DateTimeInterface $tuesdayFrom): void
    {
        $this->tuesdayFrom = $tuesdayFrom;
    }

    public function getTuesdayTo(): ?\DateTimeInterface
    {
        return $this->tuesdayTo;
    }

    public function setTuesdayTo(?\DateTimeInterface $tuesdayTo): void
    {
        $this->tuesdayTo = $tuesdayTo;
    }

    public function getWednesdayFrom(): ?\DateTimeInterface
    {
        return $this->wednesdayFrom;
    }

    public function setWednesdayFrom(?\DateTimeInterface $wednesdayFrom): void
    {
        $this->wednesdayFrom = $wednesdayFrom;
    }

    public function getWednesdayTo(): ?\DateTimeInterface
    {
        return $this->wednesdayTo;
    }

    public function setWednesdayTo(?\DateTimeInterface $wednesdayTo): void
    {
        $this->wednesdayTo = $wednesdayTo;
    }

    public function getThursdayFrom(): ?\DateTimeInterface
    {
        return $this->thursdayFrom;
    }

    public function setThursdayFrom(?\DateTimeInterface $thursdayFrom): void
    {
        $this->thursdayFrom = $thursdayFrom;
    }

    public function getThursdayTo(): ?\DateTimeInterface
    {
        return $this->thursdayTo;
    }

    public function setThursdayTo(?\DateTimeInterface $thursdayTo): void
    {
        $this->thursdayTo = $thursdayTo;
    }

    public function getFridayFrom(): ?\DateTimeInterface
    {
        return $this->fridayFrom;
    }

    public function setFridayFrom(?\DateTimeInterface $fridayFrom): void
    {
        $this->fridayFrom = $fridayFrom;
    }

    public function getFridayTo(): ?\DateTimeInterface
    {
        return $this->fridayTo;
    }

    public function setFridayTo(?\DateTimeInterface $fridayTo): void
    {
        $this->fridayTo = $fridayTo;
    }

    public function getSaturdayFrom(): ?\DateTimeInterface
    {
        return $this->saturdayFrom;
    }

    public function setSaturdayFrom(?\DateTimeInterface $saturdayFrom): void
    {
        $this->saturdayFrom = $saturdayFrom;
    }

    public function getSaturdayTo(): ?\DateTimeInterface
    {
        return $this->saturdayTo;
    }

    public function setSaturdayTo(?\DateTimeInterface $saturdayTo): void
    {
        $this->saturdayTo = $saturdayTo;
    }

    public function getSundayFrom(): ?\DateTimeInterface
    {
        return $this->sundayFrom;
    }

    public function setSundayFrom(?\DateTimeInterface $sundayFrom): void
    {
        $this->sundayFrom = $sundayFrom;
    }

    public function getSundayTo(): ?\DateTimeInterface
    {
        return $this->sundayTo;
    }

    public function setSundayTo(?\DateTimeInterface $sundayTo): void
    {
        $this->sundayTo = $sundayTo;
    }
}
