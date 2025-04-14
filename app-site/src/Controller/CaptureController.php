<?php

namespace App\Controller;

use App\Service\ApiResponseService;
use App\Service\CoupleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class CaptureController extends AbstractController {
    public function __construct(
        private readonly ApiResponseService $apiResponseService,
        private readonly CoupleService $coupleService
    ) {}

    #[Route('/capture/{id}', name: 'app_capture', methods: 'GET')]
    public function capture(int $id, UserInterface $user): Response
    {
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access capture route! Sign in first!');
        }

        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            return $this->apiResponseService->error('Couple not found');
        }
//            if ($couple->getUser() !== $user) {
//                return $this->apiResponseService->error('Not authorized');
//            }

        $cameraDevice = $couple->getCameraDevice();
        if ($cameraDevice === null) {
            return $this->apiResponseService->error('Camera not found');
        }
        if ($cameraDevice->isPaired() === false) {
            return $this->apiResponseService->error('Camera not paired');
        }

        $client = HttpClient::create();

        try {
            $url = 'http://' . $cameraDevice->getIp() . '/capture?_cb=1744097322029';
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                return $this->apiResponseService->error('Unable to capture image: ' . $response->getStatusCode());
            }

            $contentType = $response->getHeaders()['content-type'][0] ?? 'image/jpeg';

            return new StreamedResponse(function () use ($response) {
                echo $response->getContent();
            }, 200, [
                'Content-Type' => $contentType,
            ]);
        } catch (\Exception $e) {
            return $this->apiResponseService->error('Unable fetching image: ' . $e->getMessage());
        }
    }
}
