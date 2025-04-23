<?php
namespace App\Twig\Components;

use App\Entity\Detection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class DetectionCard
{
    public Detection $detection;
    public string $coupleName;
}
