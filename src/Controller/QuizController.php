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

    #[Route(path: '/quiz/add', name: 'app_quiz_add')]
    public function add(): Response
    {
        return $this->render('quiz/add.html.twig');
    }

    #[Route(path: '/quiz/save', name: 'app_quiz_add')]
    public function save(): Response
    {
        return $this->render('quiz/add.html.twig');
    }

    #[Route(path: '/quiz/remove', name: 'app_quiz_remove')]
    public function remove(): Response
    {
        return $this->render('quiz/remove.html.twig');
    }
}