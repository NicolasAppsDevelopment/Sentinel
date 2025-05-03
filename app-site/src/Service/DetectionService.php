<?php

namespace App\Service;

use App\Repository\DetectionRepository;

class DetectionService
{
    public function __construct(
        private readonly DetectionRepository $detectionRepository
    ) {}

    /**
     * Get detections by couple ID belong to a User ID passed in parameter.
     */
    public function getAllDetectionsByCoupleId(int $coupleId)
    {
        return $this->detectionRepository->findByCoupleId($coupleId);
    }

    /**
     * Get all detections for a given user ID.
     */
    public function getAllDetectionsByUser(int $userId)
    {
        return $this->detectionRepository->findByUserId($userId);
    }

    public function deleteAllDetectionsByUser(int $userId)
    {
        return $this->detectionRepository->deleteAllByUserId($userId);
    }

    public function getDetectionById(int $id)
    {
        return $this->detectionRepository->find($id);
    }

    public function countNewDetectionsSince(int $coupleId, \DateTimeInterface $since): int
    {
        return $this->detectionRepository->countNewDetectionsSince($coupleId, $since);
    }
}