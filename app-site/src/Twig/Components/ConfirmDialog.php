<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class ConfirmDialog
{
    public string $title;
    public string $message;
    public string $confirmUrl;
    public string $confirmMethod = 'POST';
}
