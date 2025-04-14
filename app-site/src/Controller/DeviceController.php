<?php

namespace App\Controller;

use App\Dto\DeviceDto;
use App\Entity\Device;
use App\Service\ApiResponseService;
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

    ) {}

    #[Route('/devices/discover', name: 'app_devices_discover', methods: 'POST')]
    public function discoverDevice(#[MapRequestPayload] DeviceDto $deviceDto): Response
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

    #[Route('/devices', name: 'app_devices')]
    public function index(): Response
    {
        return $this->render('device_manager/index.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/devices/get/{id}', name: 'app_devices_id')]
    public function getDeviceById(int $id): Response
    {
        return $this->render('device_manager/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }
}
