<?php

namespace App\Service;

use App\Entity\Couple;
use App\Repository\CoupleRepository;

class CoupleService
{
    private CoupleRepository $coupleRepository;

    public function __construct(CoupleRepository $coupleRepository)
    {
        $this->coupleRepository = $coupleRepository;
    }

    /**
     * Get all couples for a given user ID.
     */
    public function getAllCouplesByUser(int $userId): array
    {
        return $this->coupleRepository->findByUserId($userId);
    }

    public function getCoupleById(int $coupleId): Couple
    {
        return $this->coupleRepository->findOneBy(
            ['id' => $coupleId]
        );
    }

    public function getCouplesByCameraId(int $cameraId): Couple
    {
        return $this->coupleRepository->findOneBy(
            ['camera_id' => $cameraId]
        );
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
        if ($couple->isEnabled()){
            $this->coupleRepository->disableCouple($coupleId);
            return false;
        }else{
            $this->coupleRepository->enableCouple($coupleId);
            return true;
        }
    }
    
}
