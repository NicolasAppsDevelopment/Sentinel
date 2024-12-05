<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;

#[Route(path: '/account')]
class AccountController extends AbstractController
{
    #[Route(path: '/view/{id}', name: 'app_account_view', methods: ['GET'])]
    public function view(User $user): Response
    {
        return $this->render('account/view.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/remove/{id}', name: 'app_account_remove', methods: ['POST'])]
    public function remove(Request $request, User $user, UserRepository $userRepository): Response
    {
        $userRepository->remove($user, true);

        return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/edit/{id}', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_account_view', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route(path: '/save/{id}', name: 'app_account_save')]
    public function save(string $id): Response
    {

        // TODO: redirect to right account id view
        return $this->render('account/view.html.twig');
    }
}