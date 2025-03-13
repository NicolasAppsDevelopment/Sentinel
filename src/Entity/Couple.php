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
    private ?User $user_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Device $action_device_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Device $camera_device_id = null;

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
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getActionDeviceId(): ?Device
    {
        return $this->action_device_id;
    }

    public function setActionDeviceId(Device $action_device_id): static
    {
        $this->action_device_id = $action_device_id;

        return $this;
    }

    public function getCameraDeviceId(): ?Device
    {
        return $this->camera_device_id;
    }

    public function setCameraDeviceId(Device $camera_device_id): static
    {
        $this->camera_device_id = $camera_device_id;

        return $this;
    }
}
