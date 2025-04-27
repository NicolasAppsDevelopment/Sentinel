<?php

namespace App\Dto;

use App\Entity\Couple;

class CoupleDetectionDto
{
    private Couple $couple;
    private int $detectionCount;

    public function __construct(Couple $couple, int $detectionCount)
    {
        $this->couple = $couple;
        $this->detectionCount = $detectionCount;
    }

    public function getCouple(): Couple
    {
        return $this->couple;
    }

    public function getDetectionCount(): int
    {
        return $this->detectionCount;
    }
}
