<?php

namespace App\Service;

use App\Dto\ActionDeviceStatusDto;
use App\Dto\CameraDeviceStatusDto;
use App\Entity\Couple;
use App\Entity\Custom\CoupleStatus;
use App\Repository\CoupleRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CoupleService
{
    public function __construct(
        private readonly CoupleRepository $coupleRepository,
        private readonly DeviceService $deviceService,
        private readonly DetectionService $detectionService,
    ) {}

    /**
     * Get all couples for a given user ID.
     * @return Couple[]
     */
    public function getAllCouplesByUser(int $userId): array
    {
        return $this->coupleRepository->findByUserId($userId);
    }

    /**
     * Get all couples for a given user ID.
     * @return CoupleStatus[]
     */
    public function getAllCouplesWithStatusByUser(int $userId): array
    {
        $result = [];
        foreach ($this->coupleRepository->findByUserId($userId) as $couple) {
            $result[] = $this->getCoupleWithStatus($couple);
        }

        return $result;
    }

    public function getCoupleWithStatusById(int $coupleId): ?CoupleStatus
    {
        $couple = $this->coupleRepository->findOneBy(['id' => $coupleId]);
        if (null === $couple) {
            return null;
        }

        return $this->getCoupleWithStatus($couple);
    }

    public function getCoupleById(int $coupleId): ?Couple
    {
        return $this->coupleRepository->findOneBy(
            ['id' => $coupleId]
        );
    }

    public function getCoupleByActionId(int $actionId): ?Couple
    {
        return $this->coupleRepository->findOneByActionDeviceId($actionId);
    }

    public function enableDisableCouple(int $coupleId): bool
    {
        $couple = $this->getCoupleById($coupleId);
        $couple->setEnabled(!$couple->isEnabled());

        return $couple->isEnabled();

    }
    public function updateTitle(int $coupleId, string $newTitle): void
    {
        $couple = $this->getCoupleById($coupleId);

        if ('' === trim($newTitle)) {
            throw new BadRequestException('Le titre ne peut pas être vide.');
        }

        $couple->setTitle($newTitle);
    }

    public function getCoupleWithStatus(Couple $couple): CoupleStatus
    {
        return new CoupleStatus(
            couple: $couple,
            actionStatus: $this->getActionDeviceStatus($couple),
            cameraStatus: $this->getCameraDeviceStatus($couple),
            newDetectionCount: $this->detectionService->countNewDetectionsSince($couple->getId(), $couple->getLastDetectionSeekDate()),
        );
    }

    private function getActionDeviceStatus(Couple $couple): ActionDeviceStatusDto
    {
        $data = $this->deviceService->getStatus($couple->getActionDevice()->getIp());
        return new ActionDeviceStatusDto(
            rssiValue: $data['rssi'],
            buzzerEnabled: $data['buzzer']
        );
    }

    private function getCameraDeviceStatus(Couple $couple): CameraDeviceStatusDto
    {
        $data = $this->deviceService->getStatus($couple->getCameraDevice()->getIp());
        return new CameraDeviceStatusDto(
            rssiValue: $data['rssi'],
        );
    }
}
