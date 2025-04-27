<?php

namespace App\EventListener;

use App\Entity\Detection;
use App\Service\ImageManagerService;
use Doctrine\ORM\Event\PreRemoveEventArgs;

class DetectionListener
{
    public function __construct(
        private readonly ImageManagerService $imageManagerService
    ) {}

    public function preRemove(Detection $detection, PreRemoveEventArgs $args): void
    {
        $this->imageManagerService->removeDetectionImage($detection->getImageFilename());
    }
}