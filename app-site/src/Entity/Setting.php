<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\When(
        expression: "this.getMondayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The monday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "mondayTo", message: "The monday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $mondayFrom = null;

    #[Assert\When(
        expression: "this.getMondayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The monday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "mondayFrom", message: "The monday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $mondayTo = null;

    #[Assert\When(
        expression: "this.getTuesdayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The tuesday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "tuesdayTo", message: "The tuesday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $tuesdayFrom = null;

    #[Assert\When(
        expression: "this.getTuesdayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The tuesday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "tuesdayFrom", message: "The tuesday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $tuesdayTo = null;

    #[Assert\When(
        expression: "this.getWednesdayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The wednesday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "wednesdayTo", message: "The wednesday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $wednesdayFrom = null;

    #[Assert\When(
        expression: "this.getWednesdayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The wednesday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "wednesdayFrom", message: "The wednesday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $wednesdayTo = null;

    #[Assert\When(
        expression: "this.getThursdayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The thursday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "thursdayTo", message: "The thursday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $thursdayFrom = null;

    #[Assert\When(
        expression: "this.getThursdayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The thursday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "thursdayFrom", message: "The thursday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $thursdayTo = null;

    #[Assert\When(
        expression: "this.getFridayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The friday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "fridayTo", message: "The friday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $fridayFrom = null;

    #[Assert\When(
        expression: "this.getFridayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The friday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "fridayFrom", message: "The friday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $fridayTo = null;

    #[Assert\When(
        expression: "this.getSaturdayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The saturday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "saturdayTo", message: "The saturday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $saturdayFrom = null;

    #[Assert\When(
        expression: "this.getSaturdayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The saturday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "saturdayFrom", message: "The saturday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $saturdayTo = null;

    #[Assert\When(
        expression: "this.getSundayTo() !== null",
        constraints: [
            new Assert\NotBlank(message: "The sunday 'from' time must be set."),
        ]
    )]
    #[Assert\LessThanOrEqual(propertyPath: "sundayTo", message: "The sunday 'from' time must be before the 'to' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $sundayFrom = null;

    #[Assert\When(
        expression: "this.getSundayFrom() !== null",
        constraints: [
            new Assert\NotBlank(message: "The sunday 'to' time must be set."),
        ]
    )]
    #[Assert\GreaterThanOrEqual(propertyPath: "sundayFrom", message: "The sunday 'to' time must be after the 'from' time.")]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $sundayTo = null;

    #[ORM\Column(options: ['default' => false])]
    public bool $sendMail = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    public ?\DateTimeInterface $lastEmailSentAt = null;

    #[Assert\Length(min: 8, max: 32, minMessage: "The access point name must be at least 8 characters long.", maxMessage: "The access point name must be at most 32 characters long.")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]+$/',
        message: 'Your name cannot contain a special character.',
        match: false,
    )]
    public ?string $accessPointName = null;

    #[Assert\Length(min: 8, max: 32, minMessage: "The access point password must be at least 8 characters long.", maxMessage: "The access point password must be at most 32 characters long.")]
    public ?string $accessPointPassword = null;

    #[Assert\When(
        expression: "this.getAccessPointPassword() !== null",
        constraints: [
            new Assert\NotBlank(message: "You must confirm the password to change it."),
            new Assert\EqualTo(propertyPath: "accessPointPassword", message: "The passwords must match."),
        ]
    )]
    public ?string $accessPointPasswordConfirm = null;

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

    public function isSendMail(): bool
    {
        return $this->sendMail;
    }

    public function setSendMail(bool $sendMail): void
    {
        $this->sendMail = $sendMail;
    }

    public function getLastEmailSentAt(): ?\DateTimeInterface
    {
        return $this->lastEmailSentAt;
    }

    public function setLastEmailSentAt(?\DateTimeInterface $lastEmailSentAt): void
    {
        $this->lastEmailSentAt = $lastEmailSentAt;
    }

    public function getAccessPointName(): ?string
    {
        return $this->accessPointName;
    }

    public function setAccessPointName(?string $accessPointName): void
    {
        $this->accessPointName = $accessPointName;
    }

    public function getAccessPointPassword(): ?string
    {
        return $this->accessPointPassword;
    }

    public function setAccessPointPassword(?string $accessPointPassword): void
    {
        $this->accessPointPassword = $accessPointPassword;
    }

    public function getAccessPointPasswordConfirm(): ?string
    {
        return $this->accessPointPasswordConfirm;
    }

    public function setAccessPointPasswordConfirm(?string $accessPointPasswordConfirm): void
    {
        $this->accessPointPasswordConfirm = $accessPointPasswordConfirm;
    }
}
