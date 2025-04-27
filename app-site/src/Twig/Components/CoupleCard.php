<?php
namespace App\Twig\Components;

use App\Entity\Custom\CoupleStatus;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class CoupleCard
{
    public CoupleStatus $couple;
}
