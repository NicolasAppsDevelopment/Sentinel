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


    public function getDevicesById(int $coupleId): Device
    {
        return $this->deviceRepository->findOneBy(
            ['id' => $coupleId]
        );
    }

    public function getNotAppairedDevices(): array
    {
        return $this->deviceRepository->findDeviceNonAppaired();
    }
}
