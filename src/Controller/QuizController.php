<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class QuizController extends AbstractController
{
    #[Route(path: '/quiz', name: 'app_quiz')]
    public function quiz(): Response
    {
        return $this->render('quiz/quiz.html.twig');
    }
}