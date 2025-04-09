<?php

namespace App\Controller;

use App\Dto\DeviceDto;
use App\Entity\Device;
use App\Repository\CoupleRepository;
use App\Repository\DeviceRepository;
use App\Service\ApiResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class DeviceController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService
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

        $deviceWithSameMacAddress = $deviceRepository->findOneBy(["macAddress" => $deviceDto->mac]);
        if ($deviceWithSameMacAddress) {
            $deviceWithSameMacAddress->setIp($deviceDto->ip);
        } else {
            $newDevice = new Device();
            $newDevice->setIp($deviceDto->ip);
            $newDevice->setMacAddress($deviceDto->mac);
            $newDevice->setIsCamera($deviceDto->type === 'camera');
            $newDevice->setIsPaired(false);
            $this->entityManager->persist($newDevice);
        }

        $this->entityManager->flush();
        return $this->apiResponseService->ok(null);
    }

    #[Route('/devices', name: 'app_devices')]
    public function index(): Response
    {
        return $this->render('device_manager/index.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/devices/all', name: 'app_devices_all')]
    public function getAllDevices(): Response
    {
        return $this->render('device_manager/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/devices/{id}', name: 'app_devices_id')]
    public function getDeviceById(int $id): Response
    {
        return $this->render('device_manager/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

}
