<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\QuestionAnswerUserQuizAttempt;
use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\UserQuizAttempt;
use App\Form\QuestionAnswerUserQuizAttemptFormType;
use App\Form\QuizFormType;
use App\Form\SearchQuizFormType;
use App\Service\FileManagerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/favorites')]
class FavoriteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route(path: '/', name: 'app_favorites_view')]
    public function list(Request $request): Response
    {
        $favoriteQuizzes = $this->getUser()->getFavoriteQuizzes();
        $searchedQuiz = null;

        $form = $this->createForm(SearchQuizFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchedQuiz = $this->entityManager->getRepository(Quiz::class)->findByTitleInMyFavorites($data['query'], $this->getUser());
        }

        return $this->render('favorites/view.html.twig', [
            'favoriteQuizzes' => $favoriteQuizzes,
            'searchedQuiz' => $searchedQuiz,
            'searchForm' => $form,
        ]);
    }

    #[Route(path: 'add/{quizId}', name: 'app_favorites_add')]
    public function add(string $quizId): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You must be logged in to add a quiz to your favorites.');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $this->getUser()->addFavoriteQuiz($this->entityManager->getRepository(Quiz::class)->find($quizId));
        $this->entityManager->flush();

        $this->addFlash('success', 'Quiz added to your favorites.');
        return $this->redirectToRoute('app_quiz_view', ['id' => $quizId], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: 'remove/{quizId}', name: 'app_favorites_remove')]
    public function remove(string $quizId): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'You must be logged in to remove a quiz from your favorites.');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $this->getUser()->removeFavoriteQuiz($this->entityManager->getRepository(Quiz::class)->find($quizId));
        $this->entityManager->flush();

        $this->addFlash('success', 'Quiz removed from your favorites.');
        return $this->redirectToRoute('app_quiz_view', ['id' => $quizId], Response::HTTP_SEE_OTHER);
    }
}
