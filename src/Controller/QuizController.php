<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\QuestionAnswerUserQuizAttempt;
use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\UserQuizAttempt;
use App\Form\QuestionAnswerUserQuizAttemptFormType;
use App\Form\QuizFormType;
use App\Repository\UserQuizAttemptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

// TODO: add #[Route(path: '/quiz')] without breaking the redirections
class QuizController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ParameterBagInterface $parameterBag
    ) {}

    #[Route(path: '/', name: 'app_quiz_view_all')]
    public function viewAll(Request $request): Response
    {
        $trendQuiz = $this->entityManager->getRepository(Quiz::class)->getTrendQuizzes();
        $lastQuiz = $this->entityManager->getRepository(Quiz::class)->getLastQuizzes();
        $searchedQuiz = [];

        $defaultData = ['query' => ''];
        $form = $this->createFormBuilder($defaultData)
            ->add('query', TextType::class, [
                'attr' => ['placeholder' => 'Search for a quiz'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchedQuiz = $this->entityManager->getRepository(Quiz::class)->findByTitle($data['query']);
        }

        return $this->render('quiz/index.html.twig', [
            'searchedQuiz' => $searchedQuiz,
            'trendQuiz' => $trendQuiz,
            'lastQuiz' => $lastQuiz,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: 'quiz/view/{id}', name: 'app_quiz_view')]
    public function view(string $id): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        return $this->render('quiz/view.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route(path: 'quiz/play/{quizId}/{questionIndex}', name: 'app_quiz_play')]
    public function play(string $quizId, string $questionIndex, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $quizId]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        $questionIndex = intval($questionIndex);

        $question = $quiz->getQuestions()[$questionIndex];

        $form = $this->createForm(QuestionAnswerUserQuizAttemptFormType::class);
        $form->handleRequest($request);

        $userQuizAttempt = $this->entityManager->getRepository(UserQuizAttempt::class)->getUserLatestAttempt($user);

        #start a quiz
        if ($questionIndex == 0 && !$form->isSubmitted()){
            #the user has an attempt on this quiz not finished
            if ($userQuizAttempt && !$userQuizAttempt[0]->isFinished()) {
                $questionIndex = count($userQuizAttempt[0]->getQuestionAnswers());
            }
            #the user has no attempts on this quiz not finished
            else {
                $userQuizAttempt = new UserQuizAttempt();
                $userQuizAttempt->setQuiz($quiz);
                $userQuizAttempt->setUser($user);
                $userQuizAttempt->setScore(0);
                $userQuizAttempt->setFinished(false);
                $userQuizAttempt->setPlayedDate(new \DateTime());

                $entityManager->persist($userQuizAttempt);
                $entityManager->flush();
            }


        }

        else {
            # the user continues his attempt
            $userQuizAttempt = $userQuizAttempt[0];
            if (!$userQuizAttempt) {
                return new Response("Not found", 404);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $questionAnswerUserQuizAttempt = new QuestionAnswerUserQuizAttempt();
            $questionAnswerUserQuizAttempt->setAttempt($userQuizAttempt);
            $questionAnswerUserQuizAttempt->setQuestion($question);

            $answer1IsSelected = $form->get('answer1')->getData();
            $answer2IsSelected = $form->get('answer1')->getData();
            $answer3IsSelected = $form->get('answer1')->getData();
            $answer4IsSelected = $form->get('answer1')->getData();
            if ($answer1IsSelected) {
                $questionAnswerUserQuizAttempt->setAnswer($question->getAnswer1());
            }
            if ($answer2IsSelected) {
                $questionAnswerUserQuizAttempt->setAnswer($question->getAnswer2());
            }
            if ($answer3IsSelected) {
                $questionAnswerUserQuizAttempt->setAnswer($question->getAnswer3());
            }
            if ($answer4IsSelected) {
                $questionAnswerUserQuizAttempt->setAnswer($question->getAnswer4());
            }

            $entityManager->persist($questionAnswerUserQuizAttempt);
            $entityManager->flush();


            if (count($quiz->getQuestions()) > $questionIndex + 1) {
                return $this->redirectToRoute('app_quiz_play', ['quizId' => $quizId , 'questionIndex' => $questionIndex + 1], Response::HTTP_SEE_OTHER);
            } else {
                $userQuizAttempt->setFinished(true);
                return $this->redirectToRoute('app_quiz_view_all');
            }

        }

        // get first question and view it
        return $this->render('question/view.html.twig', [
            'question' => $question,
            'questionIndex' => $questionIndex,
            'form' => $form,
        ]);
    }

    #[Route(path: 'quiz/add', name: 'app_quiz_add')]
    public function add(Request $request, UserInterface $user, SluggerInterface $slugger): Response
    {
        $userInDB = $this->getUser();
        if (!$userInDB) {
            return new Response("Not authorized", 401);
        }

        $quiz = new Quiz();
        $form = $this->createForm(QuizFormType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveQuizForm($form, $userInDB, $slugger);
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
            return new Response("Not found", 404);
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            return new Response("Not authorized", 401);
        }

        $this->entityManager->remove($quiz);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_quiz_view_all');
    }

    #[Route(path: 'quiz/edit/{id}', name: 'app_quiz_edit')]
    public function edit(string $id, UserInterface $user, Request $request, SluggerInterface $slugger): Response
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            return new Response("Not authorized", 401);
        }

        $form = $this->createForm(QuizFormType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveQuizForm($form, $userInDB, $slugger);
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
     * @param SluggerInterface $slugger
     * @return RedirectResponse
     */
    public function saveQuizForm(FormInterface $form, UserInterface $userInDB, SluggerInterface $slugger): RedirectResponse
    {
        $quiz = $form->getData();
        $quiz->setAuthor($userInDB);
        $quiz->setCreatedDate(new \DateTime());

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
            $questionIndex = $quiz->getQuestions()->indexOf($question);

            $question->setQuiz($quiz);
            $question->setPosition($question->getPosition() ?? ($questionIndex + 1));
            $question->getAnswer1()->setQuestion($question);
            $question->getAnswer2()->setQuestion($question);

            if ($question->getAnswer3()) {
                $question->getAnswer3()->setQuestion($question);
            }
            if ($question->getAnswer4()) {
                $question->getAnswer4()->setQuestion($question);
            }

            $ressourceFile = $form->get('questions')[$questionIndex]['ressourceFile']->getData();
            $removeFile = $form->get('questions')[$questionIndex]['removeFile']->getData();

            // Handle file removal
            if ($removeFile && $question->getRessourceFilename()) {
                $filePath = $this->parameterBag->get("uploads_directory") . '/' . $question->getRessourceFilename();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $question->setRessourceFilename(null);
                $question->setType(0);
            }

            if ($ressourceFile) {
                $fileType = $ressourceFile->getMimeType();
                if (str_contains($fileType, 'image')) {
                    $question->setType(1);
                } elseif (str_contains($fileType, 'audio')) {
                    $question->setType(2);
                } elseif (str_contains($fileType, 'video')) {
                    $question->setType(3);
                } else {
                    $question->setType(0);
                }

                $originalFilename = pathinfo($ressourceFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $ressourceFile->guessExtension();
                try {
                    $ressourceFile->move($this->parameterBag->get("uploads_directory"), $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                if ($question->getRessourceFilename()) {
                    $oldFilePath = $this->parameterBag->get("uploads_directory") . '/' . $question->getRessourceFilename();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $question->setRessourceFilename($newFilename);
            }

            $quiz->addQuestion($question);
        }

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        $this->addFlash('success', 'Quiz updated successfully!');
        return $this->redirectToRoute('app_quiz_view', ['id' => $quiz->getId()], Response::HTTP_SEE_OTHER);
    }
}
