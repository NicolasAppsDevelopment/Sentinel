<?php

namespace App\Controller;

use App\Dto\DiscoverDeviceDto;
use App\Entity\Device;
use App\Service\ApiResponseService;
use App\Service\CoupleService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService,
        private readonly CoupleService $coupleService,

    ) {}

    #[Route('/report/{id}', name: 'app_report', methods: 'POST')]
    public function report(int $id): Response
    {
        $couple = $this->coupleService->getCoupleByCameraId($id);

        dd($couple);

        return $this->apiResponseService->ok(null);
    }
}
