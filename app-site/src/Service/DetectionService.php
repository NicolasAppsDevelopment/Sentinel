<?php

namespace App\Service;

use App\Entity\Detection;
use App\Repository\DetectionRepository;
use App\Repository\CoupleRepository;

class DetectionService
{
    private DetectionRepository $detectionRepository;
    private $coupleRepository; // Define the coupleRepository property
    public function __construct(DetectionRepository $detectionRepository, CoupleRepository $coupleRepository)
    {
        $this->detectionRepository = $detectionRepository;
        $this->coupleRepository = $coupleRepository;

    }

    /**
     * Get all detections for a given user ID.
     */

    public function getAllDetectionsByCoupleId(int $coupleId): array
    {
        return $this->detectionRepository->findByCoupleId($coupleId);
    }

    /**
     * Create a new Detection.
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
    public function createNewDetection(array $data): Detection
    {
        // Convert association_date to a DateTime
        $triggeredAt = new \DateTime($data['triggered_at']);

        return $this->detectionRepository->createDetection(
            $data['image_filename'],
            $triggeredAt,
            $data['couple'],

        );
    }

    /**
     * Delete a Detection.
     */
    public function deleteDetection(Detection $detection): void
    {
        $this->detectionRepository->remove($detection, true);
    }

    /**
     * Get detections by couple ID belong to a User ID passed in parameter.
     */
    public function getAllDetectionsByUser(int $userId)
    {
        return $this->detectionRepository->findByUserId($userId);
    }

    public function getDetectionById(int $id)
    {
        return $this->detectionRepository->find($id);
    }

}