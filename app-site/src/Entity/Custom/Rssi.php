<?php

namespace App\Entity\Custom;

class Rssi
{
    public int $value;
    public string $state;
    public string $color;

    public function __construct(?int $rssiValue)
    {
        if (null === $rssiValue) {
            $this->value = -999;
            $this->state = 'Unknown';
            $this->color = 'gray';
            return;
        }

        if ($rssiValue > -50) {
            $this->state = 'Excellent';
            $this->color = 'green';
        } elseif ($rssiValue > -70) {
            $this->state = 'Good';
            $this->color = 'orange';
        } else {
            $this->state = 'Weak';
            $this->color = 'red';
        }

        $this->value = $rssiValue;
    }
}
