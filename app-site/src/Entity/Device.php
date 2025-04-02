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

    #[ORM\Column(length: 40)]
    private ?string $mac_adress = null;

    #[ORM\Column(length: 40)]
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

    public function getMacAdress(): ?string
    {
        return $this->mac_adress;
    }

    public function setMacAdress(string $mac_adress): static
    {
        $this->mac_adress = $mac_adress;

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
