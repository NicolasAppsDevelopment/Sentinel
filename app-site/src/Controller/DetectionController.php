<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Form\CoupleFormType;
use App\Service\ApiResponseService;
use App\Service\DetectionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormInterface;

final class DetectionController extends AbstractController{

    public function __construct(
        private readonly DetectionService $detectionService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService
    ) {}


    #[Route('/detections', name: 'app_detections')]
    public function index(UserInterface $user): Response
    {
        $detections = $this->detectionService->getAllDetectionsByUser($user->getId());

        return $this->render('detection/all.html.twig', [
            'detections'=> $detections,
        ]);
    }
}
