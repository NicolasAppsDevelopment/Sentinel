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

// TODO: add #[Route(path: '/quiz')] without breaking the redirections
class QuizController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManagerService $fileManagerService
    ) {}

    #[Route(path: '/', name: 'app_quiz_view_all')]
    public function viewAll(Request $request): Response
    {
        $trendQuiz = $this->entityManager->getRepository(Quiz::class)->getTrendQuizzes();
        $lastQuiz = $this->entityManager->getRepository(Quiz::class)->getLastQuizzes();
        $searchedQuiz = null;

        $form = $this->createForm(SearchQuizFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchedQuiz = $this->entityManager->getRepository(Quiz::class)->findByTitle($data['query']);
        }

        return $this->render('quiz/all.html.twig', [
            'searchedQuiz' => $searchedQuiz,
            'trendQuiz' => $trendQuiz,
            'lastQuiz' => $lastQuiz,
            'searchForm' => $form,
        ]);
    }

    #[Route(path: '/quiz/view/me', name: 'app_quiz_view_me')]
    public function viewMe(Request $request): Response
    {
        $myQuizzes = $this->entityManager->getRepository(Quiz::class)->findBy(['author' => $this->getUser()]);
        $searchedQuiz = null;

        $form = $this->createForm(SearchQuizFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchedQuiz = $this->entityManager->getRepository(Quiz::class)->findByTitle($data['query']);
        }

        return $this->render('quiz/me.html.twig', [
            'myQuizzes' => $myQuizzes,
            'searchedQuiz' => $searchedQuiz,
            'searchForm' => $form,
        ]);
    }

    #[Route(path: 'quiz/view/{id}', name: 'app_quiz_view')]
    public function view(string $id): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            $this->addFlash('error', 'Quiz not found!');
            return $this->redirectToRoute('app_quiz_view_all');
        }

        $nbOfTimesQuizHasBeenPlayed = $this->entityManager->getRepository(Quiz::class)->getNbOfTimesPlayed($quiz->getId());

        return $this->render('quiz/view.html.twig', [
            'quiz' => $quiz,
            'nbOfTimesQuizHasBeenPlayed' => $nbOfTimesQuizHasBeenPlayed,

        ]);
    }

    #[Route(path: 'quiz/play/{quizId}', name: 'app_quiz_play')]
    public function play(string $quizId, Request $request, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $quizId]);
        if (!$quiz) {
            $this->addFlash('error', 'Quiz not found!');
            return $this->redirectToRoute('app_quiz_view_all');
        }

        $question = $quiz->getQuestions()[0];
        $lastTry = $this->entityManager->getRepository(UserQuizAttempt::class)->getUserLatestAttemptNotFinished($user, $quiz);
        if (!$lastTry) {
            $lastTry = new UserQuizAttempt();
            $lastTry->setQuiz($quiz);
            $lastTry->setUser($user);
            $lastTry->setScore(0);
            $lastTry->setFinished(false);
            $lastTry->setPlayedDate(new DateTime());
        } else {
            $lastAnsweredQuestion = $lastTry->getQuestionAnswers()->last()->getQuestion();
            $question = $quiz->getNextQuestion($lastAnsweredQuestion);
        }

        $form = $this->createForm(QuestionAnswerUserQuizAttemptFormType::class, null, [
            'answer1' => $question->getAnswer1()->getText(),
            'answer2' => $question->getAnswer2()->getText(),
            'answer3' => $question->getAnswer3()?->getText(),
            'answer4' => $question->getAnswer4()?->getText(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionAnswerUserQuizAttempt = new QuestionAnswerUserQuizAttempt();
            $questionAnswerUserQuizAttempt->setAttempt($lastTry);
            $questionAnswerUserQuizAttempt->setQuestion($question);

            $answers = [
                0 => [
                    'selected' => $form->get('answer1')->getData(),
                    'answer' => $question->getAnswer1(),
                ],
                1 => [
                    'selected' => $form->get('answer2')->getData(),
                    'answer' => $question->getAnswer2(),
                ],
                2 => [
                    'selected' => $form->get('answer3')->getData(),
                    'answer' => $question->getAnswer3(),
                ],
                3 => [
                    'selected' => $form->get('answer4')->getData(),
                    'answer' => $question->getAnswer4(),
                ],
            ];

            foreach ($answers as $answer) {
                if ($answer['selected']) {
                    $questionAnswerUserQuizAttempt->addAnswer($answer['answer']);
                    if ($answer['answer']->isCorrect()){
                        $user->setScore($user->getScore() + 1);
                    } else {
                        $user->setScore($user->getScore() - 1);
                    }
                }
            }

            if ($quiz->getNextQuestion($question) === null) {
                $lastTry->setFinished(true);
            }

            $this->entityManager->persist($questionAnswerUserQuizAttempt);
            $this->entityManager->persist($lastTry);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_quiz_check', ['quizId' => $quizId]);
        }

        $questionIndex = $quiz->getQuestions()->indexOf($question);
        return $this->render('question/view.html.twig', [
            'question' => $question,
            'questionIndex' => $questionIndex,
            'quizId' => $quizId,
            'form' => $form,
        ]);
    }

    #[Route(path: 'quiz/check/{quizId}', name: 'app_quiz_check')]
    public function check(string $quizId, Request $request, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $quizId]);
        if (!$quiz) {
            $this->addFlash('error', 'This quiz not longer exists!');
            return $this->redirectToRoute('app_quiz_view_all');
        }

        $lastTry = $this->entityManager->getRepository(UserQuizAttempt::class)->getUserLatestAttempt($user, $quiz);
        if (!$lastTry) {
            $this->addFlash('error', 'Your last attempt not longer exists!');
            return $this->redirectToRoute('app_quiz_view_all');
        }

        $lastAnsweredQuestion = $lastTry->getQuestionAnswers()->last();
        $question = $lastAnsweredQuestion->getQuestion();
        $selectedAnswers = $lastAnsweredQuestion->getAnswers();
        $questionIndex = $quiz->getQuestions()->indexOf($question);

        $answer1 = $question->getAnswer1();
        $answer2 = $question->getAnswer2();
        $answer3 = $question->getAnswer3();
        $answer4 = $question->getAnswer4();
        $questionAnswerUserQuizAttemptRepository = $this->entityManager->getRepository(QuestionAnswerUserQuizAttempt::class);
        return $this->render('question/result.html.twig', [
            'answers' => [
                0 => [
                    "isSelected" => $selectedAnswers->contains($answer1) ? true : false,
                    "answerRatio" => $questionAnswerUserQuizAttemptRepository->getPercentageOfTimesSelected($question, $answer1->getId()),
                ],
                1 => [
                    "isSelected" => $selectedAnswers->contains($answer2) ? true : false,
                    "answerRatio" => $questionAnswerUserQuizAttemptRepository->getPercentageOfTimesSelected($question, $answer2->getId()),
                ],
                2 => [
                    "isSelected" => $selectedAnswers->contains($answer3) ? true : false,
                    "answerRatio" => $questionAnswerUserQuizAttemptRepository->getPercentageOfTimesSelected($question, $answer3?->getId()),
                ],
                3 => [
                    "isSelected" => $selectedAnswers->contains($answer4) ? true : false,
                    "answerRatio" => $questionAnswerUserQuizAttemptRepository->getPercentageOfTimesSelected($question, $answer4?->getId()),
                ],
            ],
            'question' => $question,
            'questionIndex' => $questionIndex,
            'quizId' => $quizId,
            'quizzEnd' => $lastTry->isFinished(),
        ]);
    }

    #[Route(path: 'quiz/add', name: 'app_quiz_add')]
    public function add(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to add quiz! Sign in first!');
            return $this->redirectToRoute('app_quiz_view_me');
        }

        $quiz = new Quiz();
        $form = $this->createForm(QuizFormType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveQuizForm($form, $user);
        }

        // just display add page, save logic in /quiz/save !
        return $this->render('quiz/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: 'quiz/remove/{id}', name: 'app_quiz_remove')]
    public function remove(string $id, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            $this->addFlash('error', 'Quiz not found!');
            return $this->redirectToRoute('app_quiz_view_me');
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            $this->addFlash('error', 'You are not authorized to remove this quiz!');
            return $this->redirectToRoute('app_quiz_view_me');
        }

        $this->entityManager->remove($quiz);
        $this->entityManager->flush();

        $this->addFlash('success', 'Quiz removed successfully!');
        return $this->redirectToRoute('app_quiz_view_me');
    }

    #[Route(path: 'quiz/edit/{id}', name: 'app_quiz_edit')]
    public function edit(string $id, UserInterface $user, Request $request): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            $this->addFlash('error', 'Quiz not found!');
            return $this->redirectToRoute('app_quiz_view_me');
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            $this->addFlash('error', 'You are not authorized to edit this quiz!');
            return $this->redirectToRoute('app_quiz_view_me');
        }

        $form = $this->createForm(QuizFormType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveQuizForm($form, $userInDB);
        }

        // just display add page, save logic in /quiz/save !
        return $this->render('quiz/edit.html.twig', [
            'form' => $form,
            'quiz' => $quiz,
        ]);
    }

    /**
     * @param FormInterface $form
     * @param UserInterface $userInDB
     * @return RedirectResponse
     */
    public function saveQuizForm(FormInterface $form, UserInterface $userInDB): RedirectResponse
    {
        $quiz = $form->getData();
        $quiz->setAuthor($userInDB);
        $quiz->setCreatedDate(new DateTime());

        // Handle quiz illustration file upload
        $quiz->setIllustrationFilename(
            $this->fileManagerService->uploadReplaceFile(
                $form->get('illustrationFile')->getData(),
                $quiz->getIllustrationFilename()
            )
        );

        // Get existing questions from the database
        $existingQuestions = $this->entityManager
            ->getRepository(Question::class)
            ->findBy(['quiz' => $quiz]);

        // Collect IDs of current questions from the form
        $updatedQuestions = $quiz->getQuestions();

        // Determine which questions need to be removed
        foreach ($existingQuestions as $existingQuestion) {
            if (!$updatedQuestions->contains($existingQuestion)) {
                // Mark question for removal
                $this->entityManager->remove($existingQuestion);
            }
        }

        foreach ($quiz->getQuestions() as $question) {
            // remove question 3 and 4 if they are empty
            if ($question->getAnswer3() !== null && $question->getAnswer3()?->getText() === null) {
                $this->entityManager->remove($question->getAnswer3());
                $question->setAnswer3(null);
            }
            if ($question->getAnswer4() !== null && $question->getAnswer4()?->getText() === null) {
                $this->entityManager->remove($question->getAnswer4());
                $question->setAnswer4(null);
            }

            $questionIndex = $quiz->getQuestions()->indexOf($question);

            $question->setQuiz($quiz);
            $question->setPosition($question->getPosition() ?? ($questionIndex + 1));

            $ressourceFile = $form->get('questions')[$questionIndex]['ressourceFile']->getData();
            $removeFile = $form->get('questions')[$questionIndex]['removeFile']->getData();

            // Handle file removal
            if ($removeFile === true) {
                $this->fileManagerService->removeFile($question->getRessourceFilename());
                $question->setType(0);
                $question->setRessourceFilename(null);
                $ressourceFile = null;
            }

            // Handle file upload
            $question->setType(
                $this->fileManagerService->getFileType(
                    $ressourceFile,
                    $question->getType()
                )
            );
            $question->setRessourceFilename(
                $this->fileManagerService->uploadReplaceFile(
                    $ressourceFile,
                    $question->getRessourceFilename()
                )
            );

            $quiz->addQuestion($question);
        }

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        $this->addFlash('success', 'Quiz saved successfully!');
        return $this->redirectToRoute('app_quiz_view_me', ['id' => $quiz->getId()], Response::HTTP_SEE_OTHER);
    }
}
