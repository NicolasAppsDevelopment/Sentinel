<?php

namespace App\EventListener;

use App\Entity\Couple;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Couple::class)]
readonly class CoupleListener
{
    public function __construct() {}

    public function preRemove(Couple $couple, PreRemoveEventArgs $args): void
    {
        $couple->getCameraDevice()?->setIsPaired(false);
        $couple->getActionDevice()?->setIsPaired(false);
    }
}