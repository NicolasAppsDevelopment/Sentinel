<?php

namespace App\Entity\Custom;

use App\Dto\ActionDeviceStatusDto;
use App\Dto\CameraDeviceStatusDto;
use App\Entity\Couple;

class CoupleStatus
{
    public Couple $coupleEntity;
    public ActionDeviceStatusDto $actionStatus;
    public CameraDeviceStatusDto $cameraStatus;
    public Rssi $lowerRssi;
    public int $newDetectionCount;

    public function __construct(Couple $couple, ActionDeviceStatusDto $actionStatus, CameraDeviceStatusDto $cameraStatus, int $newDetectionCount)
    {
        $this->coupleEntity = $couple;
        $this->actionStatus = $actionStatus;
        $this->cameraStatus = $cameraStatus;
        $this->lowerRssi = $actionStatus->rssi->value < $cameraStatus->rssi->value ? $actionStatus->rssi : $cameraStatus->rssi;
        $this->newDetectionCount = $newDetectionCount;
    }
}
