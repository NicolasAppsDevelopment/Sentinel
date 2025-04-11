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

    public function getUnpairedActionDevices(): array
    {
        return $this->deviceRepository->findAllUnpairedAction();
    }

    public function getUnpairedCameraDevices(): array
    {
        return $this->deviceRepository->findAllUnpairedCamera();
    }

    public function cameraTakePicture(int $deviceId): void
    {
        $device = getDevicesById($deviceId);
        $ip =$device->getIp();

        $url = 'http://' . $ip . '/capture?_cb=1744097322029';

        $response = $this->client->request('GET', $url);

        // Optional: check response status or content
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to trigger camera capture: ' . $response->getStatusCode());
        }
        
    }
}
