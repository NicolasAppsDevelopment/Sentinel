<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Form\CoupleFormType;
use App\Service\ApiResponseService;
use App\Service\DetectionService;
use App\Service\ImageManagerService;
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
        private readonly ApiResponseService $apiResponseService,
    ) {}


    #[Route('/detections', name: 'app_detections')]
    public function index(UserInterface $user): Response
    {
        $detections = $this->detectionService->getAllDetectionsByUser($user->getId());

        return $this->render('detection/all.html.twig', [
            'detections'=> $detections,
        ]);
    }

    #[Route('/detections/view/{id}', name: 'app_detections_view')]
    public function viewDetection(string $id): Response
    {
        $detection = $this->detectionService->getDetectionById($id);
        if (!$detection) {
            $this->addFlash('error', 'Detection not found');
            return $this->redirectToRoute('app_detections');
        }

        return $this->render('detection/view.html.twig', [
            'detection' => $detection,
        ]);
    }

    #[Route('/detections/delete/{id}', name: 'app_detections_delete')]
    public function deleteDetection(string $id): Response
    {
        $detection = $this->detectionService->getDetectionById($id);
        if (!$detection) {
            $this->addFlash('error', 'Detection not found');
            return $this->redirectToRoute('app_detections');
        }

        $this->entityManager->remove($detection);
        $this->entityManager->flush();

        $this->addFlash('success', 'Detection deleted successfully');
        return $this->redirectToRoute('app_detections');
    }

    #[Route('/detections/image/{filename}', name: 'app_detections_image', methods: ['GET'])]
    public function getProtectedImage(string $filename, UserInterface $user): Response
    {
        // 1. Auth check
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access this stream! Sign in first!');
        }

        // read the image file
        $filePath = "/camera_pictures/" . $filename;
        if (!file_exists($filePath)) {
            return $this->apiResponseService->error('Image not found');
        }
        $response = new StreamedResponse(function() use ($filePath) {
            $handle = fopen($filePath, 'rb');
            if ($handle) {
                fpassthru($handle);
                fclose($handle);
            }
        });
        $response->headers->set('Content-Type', 'image/jpeg');
        return $response;
    }
}
