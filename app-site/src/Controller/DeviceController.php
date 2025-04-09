<?php

namespace App\Controller;

use App\Entity\Device;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeviceController extends AbstractController {
    #[Route('/devices/discover', name: 'app_devices_discover', methods: 'POST')]

    public function __construct(
        private readonly DeviceService $deviceService,


    ) {}

    public function discoverDevice(Device $device): Response
    {
        dd($device);
    }

    #[Route('/devices', name: 'app_devices')]
    public function index(): Response
    {
        return $this->render('device_manager/index.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/devices/unapparaired', name: 'app_devices_not_appaired')]
    public function getAllDevices(): Response
    {
        $device = $this->deviceService->getNotAppairedDevices();
        dd($device);
        return $this->render('device/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
            'devices'=> $device,
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
