<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeviceController extends AbstractController{
    #[Route('/device', name: 'app_device')]
    public function index(): Response
    {
        return $this->render('device_manager/index.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/device/all', name: 'app_device')]
    public function getAllDevice(): Response
    {
        return $this->render('device_manager/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

    #[Route('/device/{id}', name: 'app_device')]
    public function getDevice(): Response
    {
        

        return $this->render('device_manager/all.html.twig', [
            'controller_name' => 'DeviceManagerController',
        ]);
    }

}
