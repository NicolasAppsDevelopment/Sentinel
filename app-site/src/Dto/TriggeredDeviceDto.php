<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TriggeredDeviceDto {
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $mac,

        #[Assert\NotBlank]
        public readonly string  $ip,
    ) {}
}
