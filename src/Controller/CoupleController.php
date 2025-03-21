<?php

namespace App\Controller;

use App\Service\CoupleService;
use App\Service\DetectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class CoupleController extends AbstractController{

    public function __construct(
        private readonly CoupleService $coupleService,
        private readonly DetectionService $detectionService,


    ) {}


    #[Route('/couple', name: 'app_couple')]
    public function index(): Response
    {
        return $this->render('couple/index.html.twig', [
            'controller_name' => 'CoupleController',
        ]);
    }

    #[Route('/couple/{id}', name: 'app_get_couple')]
    public function getCouple($id): Response
    {
        $couple = $this->coupleService->getCouplesById($id);
        $detections = $this->detectionService->getAllDetectionsByCoupleId($id);

        // dd($detections);
        return $this->render('couple/alarme.html.twig', [
            'controller_name' => 'CoupleController',
            'deviceInfo'=> $couple,
            'detections'=> $detections,

        ]);
    }

    #[Route('/couples/all', name: 'app_couple_getall')]
    public function getAllDevice(UserInterface $user): Response
    {
        $couple = $this->coupleService->getAllCouplesByUser($user->getId());

        //dd($couple);
        return $this->render('couple/all.html.twig', [
            'controller_name' => 'CoupleController',
            'couple'=> $couple,
        ]);
    }
}
