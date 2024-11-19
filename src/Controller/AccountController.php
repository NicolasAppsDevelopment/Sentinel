<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
#[Route(path: '/account')]
class AccountController extends AbstractController
{
    #[Route(path: '/view/{id}', name: 'app_account_view')]
    public function view(string $id): Response
    {

        return $this->render('account/view.html.twig');
    }

    #[Route(path: '/remove/{id}', name: 'app_account_remove')]
    public function remove(string $id): Response
    {
        // also remove associated data

        return $this->render('register.html.twig');
    }

    #[Route(path: '/edit/{id}', name: 'app_account_edit')]
    public function edit(string $id): Response
    {
        // just diplay edit page, save logic in /account/save !


        return $this->render('account/edit.html.twig');
    }

    #[Route(path: '/save/{id}', name: 'app_account_save')]
    public function save(string $id): Response
    {

        // TODO: redirect to right account id view
        return $this->render('account/view.html.twig');
    }
}