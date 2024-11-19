<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/quiz')]
class QuizController extends AbstractController
{
    #[Route(path: '/view/all', name: 'app_quiz_view_all')]
    public function viewAll(): Response
    {
        return $this->render('quiz/index.html.twig');
    }

    #[Route(path: '/view/{id}', name: 'app_quiz_view')]
    public function view(string $id): Response
    {
        return $this->render('quiz/view.html.twig');
    }

    #[Route(path: '/add', name: 'app_quiz_add')]
    public function add(): Response
    {
        // just diplay add page, save logic in /quiz/save !

        return $this->render('quiz/add.html.twig');
    }

    #[Route(path: '/remove/{id}', name: 'app_quiz_remove')]
    public function remove(string $id): Response
    {
        return $this->render('quiz/index.html.twig');
    }

    #[Route(path: '/edit/{id}', name: 'app_quiz_edit')]
    public function edit(string $id): Response
    {
        // just diplay edit page, save logic in /quiz/save !

        return $this->render('quiz/edit.html.twig');
    }

    #[Route(path: '/save/{id}', name: 'app_quiz_save')]
    public function save(string $id): Response
    {

        // TODO: redirect to right quiz id view
        return $this->render('quiz/view.html.twig');
    }

    #[Route(path: '/{id}/add/question', name: 'app_question_add')]
    public function addQuestion(string $id): Response
    {
        // just diplay add page, save logic in /question/save !

        return $this->render('question/add.html.twig');
    }
}