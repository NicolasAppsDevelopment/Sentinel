<?php

namespace App\Service;

use App\Entity\Device;
use App\Repository\DeviceRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DeviceService
{
    public function __construct(
        private readonly DeviceRepository $deviceRepository
    ) {}

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

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    public function getStatus(string $ip): array {
        $url = 'http://' . $ip . '/status';

        $client = HttpClient::create();
        $actionDeviceResponse = $client->request('GET', $url);

        if ($actionDeviceResponse->getStatusCode() !== 200) {
            throw new Exception('Unable to get action status: ' . $actionDeviceResponse->getStatusCode());
        }

        return $actionDeviceResponse->toArray();
    }
}
