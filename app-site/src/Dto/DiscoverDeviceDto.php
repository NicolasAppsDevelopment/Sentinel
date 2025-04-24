<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class DiscoverDeviceDto {
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $mac,

        #[Assert\NotBlank]
        public readonly string  $ip,

        #[Assert\NotBlank]
        public readonly string $type
    ) {}
}
