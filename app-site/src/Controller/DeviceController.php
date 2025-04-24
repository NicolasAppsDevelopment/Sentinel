<?php

namespace App\Controller;

use App\Dto\DiscoverDeviceDto;
use App\Entity\Detection;
use App\Entity\Device;
use App\Service\ApiResponseService;
use App\Service\CoupleService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class DeviceController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService,
        private readonly DeviceService $deviceService,
        private readonly CoupleService $coupleService,
        private readonly CoupleController $coupleController,

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

    //TODO Save image (by using extract function in coupleController and had path to detection entity
    #[Route(path: '/devices/{deviceId}/triggered', name: 'action_device_trigger')]
    public function triggered(int $deviceId, UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        $couple = $this->coupleService->getCoupleByActionId($deviceId);

        $response = $this->coupleController->getSecureCapture($couple->getId(), $user);


        // Get image content
        ob_start();
        $response->sendContent();
        $imageData = ob_get_clean();

        if (!$imageData) {
            return $this->apiResponseService->error('Failed to capture image content.');
        }

        // Generate unique filename
        $date = new \DateTime();
        $filename = $date->format('Y-m-d_H-i-s') . '.jpeg';

        // Define save path
        $saveDir = $this->getParameter('kernel.project_dir') . '/public/pictures/';
        $fullPath = $saveDir . $filename;

        // Save image to disk
        file_put_contents($fullPath, $imageData);


        $detection = new Detection();
        $detection->setCouple($couple);
        $detection->setImageFilename($filename);
        $entityManager->persist($detection);

        $entityManager->flush();


        return $this->apiResponseService->okRaw('Image captured and saved.');
    }
}
