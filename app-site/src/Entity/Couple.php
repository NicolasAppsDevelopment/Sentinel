<?php

namespace App\Entity;

use App\Repository\CoupleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    private ?\DateTimeInterface $associationDate = null;

    #[ORM\ManyToOne(inversedBy: 'couples', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $lastDetectionSeekDate = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Device $actionDevice = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Device $cameraDevice = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\OneToMany(mappedBy: 'couple', targetEntity: Detection::class, cascade: ['remove'])]
    private Collection $detections;

    public function __construct()
    {
        $this->associationDate = new \DateTime();
        $this->lastDetectionSeekDate = new \DateTime();
        $this->detections = new ArrayCollection();
    }

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
        return $this->associationDate;
    }

    public function setAssociationDate(\DateTimeInterface $associationDate): static
    {
        $this->associationDate = $associationDate;

        return $this;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getActionDevice(): ?Device
    {
        return $this->actionDevice;
    }

    public function setActionDevice(Device $actionDevice): static
    {
        $this->actionDevice = $actionDevice;

        return $this;
    }

    public function getCameraDevice(): ?Device
    {
        return $this->cameraDevice;
    }

    public function setCameraDevice(Device $cameraDevice): static
    {
        $this->cameraDevice = $cameraDevice;

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

    public function getLastDetectionSeekDate(): ?\DateTimeInterface
    {
        return $this->lastDetectionSeekDate;
    }

    public function setLastDetectionSeekDate(\DateTimeInterface $lastDetectionSeekDate): static
    {
        $this->lastDetectionSeekDate = $lastDetectionSeekDate;

        return $this;
    }

    /**
     * @return Collection<int, Detection>
     */
    public function getDetections(): Collection
    {
        return $this->detections;
    }

    public function addDetection(Detection $detection): self
    {
        if (!$this->detections->contains($detection)) {
            $this->detections[] = $detection;
            $detection->setCouple($this);
        }

        return $this;
    }

    public function removeDetection(Detection $detection): self
    {
        if ($this->detections->removeElement($detection)) {
            if ($detection->getCouple() === $this) {
                $detection->setCouple(null);
            }
        }

        return $this;
    }
}
