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


    #[Route('/couples', name: 'app_couples')]
    public function index(UserInterface $user): Response
    {
        $couple = $this->coupleService->getAllCouplesByUser($user->getId());
        //dd($couple[0]->detections);
        $wifi = "good";

        //dd($couple);
        return $this->render('couple/all.html.twig', [
            'controller_name' => 'CoupleController',
            'couple'=> $couple,
            'wifi'=>$wifi,
        ]);
    }

    #[Route('/couples/{id}', name: 'app_couples_id')]
    public function getCoupleById(int $id): Response
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
}
