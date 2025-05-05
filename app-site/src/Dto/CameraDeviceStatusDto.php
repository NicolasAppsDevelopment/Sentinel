<?php
namespace App\Dto;

use App\Entity\Custom\Rssi;

class CameraDeviceStatusDto {
    public readonly Rssi $rssi;
    public function __construct(
        ?int $rssiValue,
    ) {
        $this->rssi = new Rssi($rssiValue);
    }
}
