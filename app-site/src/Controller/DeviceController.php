<?php

namespace App\Controller;

use App\Dto\DiscoverDeviceDto;
use App\Dto\TriggeredDeviceDto;
use App\Entity\Detection;
use App\Entity\Device;
use App\Service\ApiResponseService;
use App\Service\CoupleService;
use App\Service\ImageManagerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class DeviceController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService,
        private readonly DeviceService $deviceService,
        private readonly CoupleService $coupleService,
        private readonly ImageManagerService $imageManagerService,
    ) {}

    #[Route('/devices/discover', name: 'app_devices_discover', methods: 'POST')]
    public function discoverDevice(#[MapRequestPayload] DiscoverDeviceDto $deviceDto): Response
    {
        // Check if the device already exists
        $deviceRepository = $this->entityManager->getRepository(Device::class);

        $deviceWithSameIp = $deviceRepository->findOneBy(["ip" => $deviceDto->ip]);
        if ($deviceWithSameIp) {
            $this->entityManager->remove($deviceWithSameIp);
        }

        $device = $deviceRepository->findOneBy(["macAddress" => $deviceDto->mac]);
        if ($device) {
            $device->setIp($deviceDto->ip);
        } else {
            $device = new Device();
            $device->setIp($deviceDto->ip);
            $device->setMacAddress($deviceDto->mac);
            $device->setIsCamera($deviceDto->type === 'camera');
            $device->setIsPaired(false);
        }

        $this->entityManager->persist($device);
        $this->entityManager->flush();

        return $this->apiResponseService->okRaw($device->getId());
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/devices/triggered', name: 'action_device_trigger', methods: 'POST')]
    public function deviceTriggered(#[MapRequestPayload] TriggeredDeviceDto $deviceDto, EntityManagerInterface $entityManager): Response
    {
        $device = $this->deviceService->getDeviceByIpAndMac($deviceDto->ip, $deviceDto->mac);
        if (!$device) {
            return $this->apiResponseService->error('Failed to retrieve device.');
        }

        $couple = $this->coupleService->getCoupleByActionId($device->getId());
        if (!$couple) {
            return $this->apiResponseService->error('Failed to retrieve couple.');
        }

        if (!$couple->isEnabled()) {
            return $this->apiResponseService->error('Couple is disabled.');
        }

        $filename = $this->imageManagerService->saveDetectionImage($couple->getCameraDevice()->getIp());

        $detection = new Detection();
        $detection->setCouple($couple);
        $detection->setImageFilename($filename);
        $entityManager->persist($detection);

        $entityManager->flush();


        return $this->apiResponseService->ok(null);
    }
}
