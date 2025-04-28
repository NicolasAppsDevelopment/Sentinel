<?php

namespace App\Entity\Custom;

use App\Dto\ActionDeviceStatusDto;
use App\Dto\CameraDeviceStatusDto;
use App\Entity\Couple;

class Rssi
{
    public int $value;
    public string $state;
    public string $color;

    public function __construct(int $rssiValue)
    {
        $this->value = $rssiValue;
        $this->state = $this->toString($rssiValue);
        $this->color = $this->toColor($rssiValue);
    }

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

    public function toColor(int $rssi): string
    {
        if ($rssi > -50) {
            return 'green';
        } elseif ($rssi > -70) {
            return 'orange';
        } else {
            return 'red';
        }
    }
}
