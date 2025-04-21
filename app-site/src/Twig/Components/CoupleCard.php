<?php
namespace App\Twig\Components;

use App\Entity\Couple;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class CoupleCard
{
    public Couple $couple;
}
