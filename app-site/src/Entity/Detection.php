<?php

namespace App\Entity;

use App\Repository\DetectionRepository;
use App\Service\ImageManagerService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetectionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Detection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFilename = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $triggeredAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Couple $couple = null;

    public function __construct(
        public readonly ImageManagerService $imageManagerService,
    )
    {
        $this->triggeredAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getTriggeredAt(): ?\DateTimeInterface
    {
        return $this->triggeredAt;
    }

    public function setTriggeredAt(\DateTimeInterface $triggeredAt): static
    {
        $this->triggeredAt = $triggeredAt;

        return $this;
    }

    public function getCouple(): ?Couple
    {
        return $this->couple;
    }

    public function setCouple(?Couple $couple): static
    {
        $this->couple = $couple;

        return $this;
    }

    #[ORM\PreRemove]
    public function removeImage(): void
    {
        $this->imageManagerService->removeDetectionImage($this->imageFilename);;
    }
}
