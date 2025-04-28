<?php

namespace App\Service;

readonly class RssiStateService
{
    public function toString(int $rssi): string
    {
        if ($rssi > -50) {
            return 'Excellent';
        } elseif ($rssi > -70) {
            return 'Good';
        } else {
            return 'Weak';
        }
    }
}
