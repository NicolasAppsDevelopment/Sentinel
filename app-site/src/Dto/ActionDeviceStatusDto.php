<?php
namespace App\Dto;

use App\Service\RssiStateService;

class ActionDeviceStatusDto {

    public readonly string $rssiState;
    public readonly int $rssiValue;
    public readonly bool $buzzerEnabled;
    public function __construct(
        int $rssiValue,
        string $buzzerEnabled,
    ) {
        $this->rssiState = (new RssiStateService())->toString($rssiValue);
        $this->rssiValue = $rssiValue;
        $this->buzzerEnabled = $buzzerEnabled === 'on';
    }
}
