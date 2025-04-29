<?php

namespace App\Controller;

use App\Service\ApiResponseService;
use App\Service\DetectionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final class DetectionController extends AbstractController{

    public function __construct(
        private readonly DetectionService $detectionService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService,
        private readonly ParameterBagInterface $parameterBag,
    ) {}


    #[Route('/detections', name: 'app_detections')]
    public function index(UserInterface $user, PaginatorInterface $paginator, Request $request): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You need to sign in to see the detections !');
            return $this->redirectToRoute('app_login');
        }

        $query = $this->detectionService->getAllDetectionsByUser($user->getId());

        $detections = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // current page, default 1
            15 // number of items per page
        );

        return $this->render('detection/all.html.twig', [
            'detections'=> $detections,
        ]);
    }

    #[Route('/detections/view/{id}', name: 'app_detections_view')]
    public function viewDetection(string $id, UserInterface $user): Response
    {
        $detection = $this->detectionService->getDetectionById($id);
        if (!$detection) {
            $this->addFlash('error', 'Detection not found');
            return $this->redirectToRoute('app_detections');
        }

        // Check authorization
        if (!$user) {
            $this->addFlash('error', 'You need to sign in to see this detection !');
            return $this->redirectToRoute('app_login');
        }
        if ($user->getUserIdentifier() != $detection->getCouple()->getUser()->getUsername()) {
            $this->addFlash('error', 'You are not authorized to see this detection !');
            return $this->redirectToRoute('app_couples');
        }

        return $this->render('detection/view.html.twig', [
            'detection' => $detection,
        ]);
    }

    #[Route('/detections/delete/{id}', name: 'app_detections_delete')]
    public function deleteDetection(string $id, UserInterface $user): Response
    {
        $detection = $this->detectionService->getDetectionById($id);
        if (!$detection) {
            $this->addFlash('error', 'Detection not found');
            return $this->redirectToRoute('app_detections');
        }

        // Check authorization
        if (!$user) {
            $this->addFlash('error', 'You need to sign in to delete this detection !');
            return $this->redirectToRoute('app_login');
        }
        if ($user->getUserIdentifier() != $detection->getCouple()->getUser()->getUsername()) {
            $this->addFlash('error', 'You are not authorized to delete this detection !');
            return $this->redirectToRoute('app_couples');
        }

        $this->entityManager->remove($detection);
        $this->entityManager->flush();

        $this->addFlash('success', 'Detection deleted successfully');
        return $this->redirectToRoute('app_detections');
    }

    #[Route('/detections/image/{filename}', name: 'app_detections_image', methods: ['GET'])]
    public function getProtectedImage(string $filename, UserInterface $user): Response
    {
        // Auth check
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to access this stream! Sign in first!');
            return $this->redirectToRoute('app_login');
        }


        // read the image file
        $filePath = $this->parameterBag->get('detections_dir') . "/" . $filename;
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
