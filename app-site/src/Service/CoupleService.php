<?php

namespace App\Service;

use App\Dto\ActionDeviceStatusDto;
use App\Dto\CameraDeviceStatusDto;
use App\Dto\CoupleStatusDto;
use App\Entity\Couple;
use App\Repository\CoupleRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CoupleService
{
    public function __construct(
        private readonly CoupleRepository $coupleRepository,
        private readonly DeviceService $deviceService,
        private readonly RssiStateService $rssiStateService,
    ) {}

    /**
     * Get all couples for a given user ID.
     */
    public function getAllCouplesByUser(int $userId): array
    {
        return $this->coupleRepository->findByUserId($userId);
    }

    public function getCoupleById(int $coupleId): ?Couple
    {
        return $this->coupleRepository->findOneBy(
            ['id' => $coupleId]
        );
    }

    public function getCoupleByCameraId(int $cameraId): ?Couple
    {
        return $this->coupleRepository->findOneByCameraDeviceId($cameraId);
    }

    public function getCoupleByActionId(int $actionId): ?Couple
    {
        return $this->coupleRepository->findOneByActionDeviceId($actionId);
    }

    /**
     * Create a new Couple.
     * 
     * $data might look like:
     * [
     *   'action_device_id'   => 1,
     *   'camera_device_id'   => 2,
     *   'title'              => 'My Title',
     *   'association_date'   => '2025-03-17',
     *   'user_id'            => 123
     * ]
     */
    public function createNewCouple(array $data): Couple
    {
        // Convert association_date to a DateTime
        $associationDate = new \DateTime($data['association_date']);

        return $this->coupleRepository->createCouple(
            $data['action_device_id'],
            $data['camera_device_id'],
            $data['title'],
            $associationDate,
            $data['user_id']
        );
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

    public function getStatus(Couple $couple): CoupleStatusDto
    {
        return new CoupleStatusDto(
            actionDeviceStatus: $this->getActionDeviceStatus($couple),
            cameraDeviceStatus: $this->getCameraDeviceStatus($couple),
        );
    }

    private function getActionDeviceStatus(Couple $couple): ActionDeviceStatusDto
    {
        $data = $this->deviceService->getStatus($couple->getActionDevice()->getIp());
        return new ActionDeviceStatusDto(
            rssiState: $this->rssiStateService->toString($data['rssi']),
            buzzer: $data['buzzer']
        );
    }

    private function getCameraDeviceStatus(Couple $couple): CameraDeviceStatusDto
    {
        $data = $this->deviceService->getStatus($couple->getCameraDevice()->getIp());
        return new CameraDeviceStatusDto(
            rssiState: $this->rssiStateService->toString($data['rssi']),
        );
    }
}
