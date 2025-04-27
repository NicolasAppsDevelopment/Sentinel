<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CoupleStatusDto {
    public function __construct(
        #[Assert\NotBlank]
        public readonly ActionDeviceStatusDto $actionDeviceStatus,

        #[Assert\NotBlank]
        public readonly CameraDeviceStatusDto $cameraDeviceStatus,
    ) {}
}
