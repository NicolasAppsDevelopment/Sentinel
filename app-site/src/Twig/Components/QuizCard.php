<?php
namespace App\Twig\Components;

use App\Entity\Quiz;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class QuizCard
{
    public Quiz $quiz;
}
