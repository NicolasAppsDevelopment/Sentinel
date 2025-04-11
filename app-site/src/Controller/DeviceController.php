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

    #[Route('/devices/unpaired', name: 'app_devices_not_appaired')]
    public function getUnpairedDevices(): Response
    {
        $unpairedDevices = $this->deviceService->getUnpairedDevices();
        return $this->render('device/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
            'devices'=> $unpairedDevices,
        ]);
    }


    #[Route('/devices/get/{id}', name: 'app_devices_id')]
    public function getDeviceById(int $id): Response
    {
        return $this->render('device_manager/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/devices/{id}/capture', name: 'app_devices_capture')]
    public function deviceTakePicture(int $id): Response
    {
        $this->deviceService->cameraTakePicture($id);
        return $this->render('couples/alarme.html.twig', [
            'allphoto' => $this->deviceService->getAllPhotoByDeviceId($id)
        ]);
    }

}
