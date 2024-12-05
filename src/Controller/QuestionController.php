<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/question')]
class QuestionController extends AbstractController
{
    #[Route(path: '/view/{id}', name: 'app_question_view')]
    public function view(string $id): Response
    {

        return $this->render('question/view.html.twig');
    }

    #[Route(path: '/answer/{id}', name: 'app_question_answer')]
    public function answer(string $id): Response
    {

        // TODO: redirect next question or to quiz result
        return $this->render('question/view.html.twig');
    }

    #[Route(path: '/remove/{id}', name: 'app_question_remove')]
    public function remove(string $id): Response
    {
        // also remove answers here

        // TODO: redirect to right quiz id edit
        return $this->render('quiz/edit.html.twig');
    }

    #[Route(path: '/edit/{id}', name: 'app_question_edit')]
    public function edit(string $id): Response
    {
        // just diplay edit page, save logic in /question/save !

        return $this->render('question/edit.html.twig');
    }

    #[Route(path: '/save/{id}', name: 'app_question_save')]
    public function save(string $id): Response
    {
        // also save answers here (see Symfony dynamic collection)


        // TODO: redirect to right quiz id edit
        return $this->render('quiz/edit.html.twig');
    }
}
