<?php

namespace App\Service;

use App\Entity\Couple;
use App\Repository\CoupleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class RssiStateService
{
    public function toString(int $rssi): string
    {
        if ($rssi > -50) {
            return 'Excellent';
        } elseif ($rssi > -70) {
            return 'Good';
        } else {
            return 'Very Weak';
        }
    }
}
