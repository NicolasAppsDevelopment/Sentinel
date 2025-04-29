<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

#[Route(path: '/account')]
class AccountController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route(path: '/remove', name: 'app_account_remove', methods: ['GET','POST'])]
    public function remove(UserInterface $loginUser, TokenStorageInterface $tokenStorage, RequestStack $requestStack): Response
    {
        if (!$loginUser) {
            $this->addFlash('error', 'You need to sign in to remove an account !');
            return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
        }

        $this->entityManager->remove($loginUser);
        $this->entityManager->flush();

        // Clear the security token (logout manually)
        $tokenStorage->setToken(null);

        // Invalidate the session
        $session = $requestStack->getSession();
        $session->invalidate();

        $this->addFlash('success', 'Account deleted successfully !');
        return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/edit/{id}', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(UserInterface $loginUser, Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, int $id): Response
    {
        // Check authorization
        if (!$loginUser) {
            $this->addFlash('error', 'You need to sign in to edit this account !');
            return $this->redirectToRoute('app_login');
        }
        if ($user->getUserIdentifier() != $loginUser->getUserIdentifier()) {
            $this->addFlash('error', 'You are not authorized to edit this account !');
            return $this->redirectToRoute('app_couples');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword) {
                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }


            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account edited successfully !');
            return $this->redirectToRoute('app_account_edit', array('id' => $user->getId()), Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
