<?php
namespace App\Dto;

use App\Entity\Custom\Rssi;


class ActionDeviceStatusDto {

    public readonly Rssi $rssi;
    public readonly bool $buzzerEnabled;
    public function __construct(
        ?int $rssiValue,
        string $buzzerEnabled,
    ) {
        $this->rssi = new Rssi($rssiValue);
        $this->buzzerEnabled = $buzzerEnabled === 'on';
    }
}
