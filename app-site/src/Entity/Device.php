<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 17, unique: true)]
    private ?string $macAddress = null;

    #[ORM\Column(length: 15, unique: true)]
    private ?string $ip = null;

    #[ORM\Column]
    private ?bool $isCamera = null;

    #[ORM\Column]
    private ?bool $isPaired = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getMacAddress(): ?string
    {
        return $this->macAddress;
    }

    public function setMacAddress(string $macAddress): static
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function isCamera(): ?bool
    {
        return $this->isCamera;
    }

    public function setIsCamera(bool $isCamera): static
    {
        $this->isCamera = $isCamera;

        return $this;
    }

    public function isPaired(): ?bool
    {
        return $this->isPaired;
    }

    public function setIsPaired(bool $isPaired): static
    {
        $this->isPaired = $isPaired;

        return $this;
    }
}

