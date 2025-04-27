<?php
namespace App\Dto;

use App\Service\RssiStateService;

class CameraDeviceStatusDto {
    public readonly string $rssiState;
    public readonly int $rssiValue;
    public function __construct(
        int $rssiValue,
    ) {
        $this->rssiState = (new RssiStateService())->toString($rssiValue);
        $this->rssiValue = $rssiValue;
    }
}
