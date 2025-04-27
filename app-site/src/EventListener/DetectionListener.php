<?php

namespace App\EventListener;

use App\Entity\Detection;
use App\Service\ImageManagerService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Detection::class)]
readonly class DetectionListener
{
    public function __construct(
        private ImageManagerService $imageManagerService
    ) {}

    public function preRemove(Detection $detection, PreRemoveEventArgs $args): void
    {
        $this->imageManagerService->removeDetectionImage($detection->getImageFilename());
    }
}