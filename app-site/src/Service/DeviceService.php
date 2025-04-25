<?php

namespace App\Service;

use App\Entity\Device;
use App\Repository\DeviceRepository;

class DeviceService
{
    private DeviceRepository $deviceRepository;

    public function __construct(DeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }


    public function getDeviceById(int $deviceId): ?Device
    {
        return $this->deviceRepository->findOneBy(
            ['id' => $deviceId]
        );
    }

    public function getDeviceByIpAndMac(string $ip, string $mac): ?Device
    {
        return $this->deviceRepository->findOneBy([
            'ip' => $ip,
            'macAddress' => $mac,
        ]);
    }

    public function getUnpairedActionDevices(): array
    {
        return $this->deviceRepository->findAllUnpairedAction();
    }

    public function getUnpairedCameraDevices(): array
    {
        return $this->deviceRepository->findAllUnpairedCamera();
    }
}
