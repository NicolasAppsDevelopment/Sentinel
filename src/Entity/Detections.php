<?php

namespace App\Entity;

use App\Repository\DetectionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetectionsRepository::class)]
class Detections
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_filename = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $triggered_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Couple $couple_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFilename(): ?string
    {
        return $this->image_filename;
    }

    public function setImageFilename(?string $image_filename): static
    {
        $this->image_filename = $image_filename;

        return $this;
    }

    public function getTriggeredAt(): ?\DateTimeInterface
    {
        return $this->triggered_at;
    }

    public function setTriggeredAt(\DateTimeInterface $triggered_at): static
    {
        $this->triggered_at = $triggered_at;

        return $this;
    }

    public function getCoupleId(): ?Couple
    {
        return $this->couple_id;
    }

    public function setCoupleId(?Couple $couple_id): static
    {
        $this->couple_id = $couple_id;

        return $this;
    }
}
