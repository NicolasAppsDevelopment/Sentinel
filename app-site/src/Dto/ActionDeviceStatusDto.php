<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ActionDeviceStatusDto {
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $rssiState,

        #[Assert\NotBlank]
        public readonly string $buzzer,
    ) {}
}
