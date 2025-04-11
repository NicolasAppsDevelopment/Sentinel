<?php

namespace App\Service;

use App\Entity\Detections;
use App\Repository\DetectionsRepository;

class DetectionService
{
    private DetectionsRepository $detectionRepository;

    public function __construct(DetectionsRepository $detectionRepository)
    {
        $this->detectionRepository = $detectionRepository;
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
    public function createNewDetection(array $data): Detections
    {
        // Convert association_date to a DateTime
        $triggered_at = new \DateTime($data['triggered_at']);

        return $this->detectionRepository->createDetection(
            $data['image_filename'],
            $triggered_at,
            $data['couple'],

        );
    }
}
