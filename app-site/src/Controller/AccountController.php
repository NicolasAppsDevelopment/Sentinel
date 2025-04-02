<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserQuizAttemptRepository;
use App\Form\UserType;

#[Route(path: '/account')]
class AccountController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    // #[Route(path: '/view/{id}', name: 'app_account_view', methods: ['GET'])]
    // public function view(User $user): Response
    // {
    //     //$nbQuestionsAnswered = $userQuizAttemptRepository->getUserNbQuestionsAnswered($user);
    //     $percentageOfCorrectAnswers = "No questions answered";

    //     //$nbAnswerSubmitted = $userQuizAttemptRepository->getUserNbAnswerSubmited($user);

    //     // if (count($nbQuestionsAnswered) != 0) {
    //     //     $percentageOfCorrectAnswers = round((count($userQuizAttemptRepository->getUserNbQuestionsAnsweredCorrectly($user)) * 100) / count($nbAnswerSubmitted), 0, PHP_ROUND_HALF_UP);
    //     //     $percentageOfCorrectAnswers = $percentageOfCorrectAnswers . "%";
    //     // }

    //     // return $this->render('account/view.html.twig', [
    //     //     'user' => $user,
    //     //     'NbQuizzesPlayed' => count($userQuizAttemptRepository->getUserNbQuizzesPlayed($user)),
    //     //     'NbQuestionsAnswered' => count($nbQuestionsAnswered),
    //     //     'PercentageOfCorrectAnswers' => $percentageOfCorrectAnswers,
    //     // ]);
    // }

    #[Route(path: '/remove', name: 'app_account_remove', methods: ['POST'])]
    public function remove(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
        }

        $this->entityManager->remove($this->getUser());
        $this->entityManager->flush();

        // TODO: remove user in cache

        return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/edit/{id}', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_account_view', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
