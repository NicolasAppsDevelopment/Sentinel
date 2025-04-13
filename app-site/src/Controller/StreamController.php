<?php

namespace App\Controller;

use App\Service\ApiResponseService;
use App\Service\CoupleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class StreamController extends AbstractController {
    public function __construct(
        private readonly ApiResponseService $apiResponseService,
        private readonly CoupleService $coupleService
    ) {}

    #[Route('/stream/{id}', name: 'app_stream', methods: 'GET')]
    public function getStream(int $id, UserInterface $user): StreamedResponse
    {
        return new StreamedResponse(function () use ($user, $id) {
            if (!$user) {
                return $this->apiResponseService->error('You are not authorized to access this stream! Sign in first!');
            }

            $couple = $this->coupleService->getCoupleById($id);
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

            $stream = fopen('http://' . $cameraDevice->getIp() . ':81/stream', 'rb');

            while (!feof($stream)) {
                echo fread($stream, 1024);
                flush();
            }

            fclose($stream);
            return $this->apiResponseService->error('Camera stream has been closed');
        }, 200, [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}
