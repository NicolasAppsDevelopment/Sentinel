<?php

namespace App\Entity;

use App\Repository\CoupleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoupleRepository::class)]
class Couple
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $association_date = null;

    #[ORM\ManyToOne(inversedBy: 'couples')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Device $action_device = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Device $camera_device = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAssociationDate(): ?\DateTimeInterface
    {
        return $this->association_date;
    }

    public function setAssociationDate(\DateTimeInterface $association_date): static
    {
        $this->association_date = $association_date;

        return $this;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getActionDeviceId(): ?Device
    {
        return $this->action_device;
    }

    public function setActionDeviceId(Device $action_device): static
    {
        $this->action_device = $action_device;

        return $this;
    }

    public function getCameraDeviceId(): ?Device
    {
        return $this->camera_device;
    }

    public function setCameraDeviceId(Device $camera_device): static
    {
        $this->camera_device = $camera_device;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
