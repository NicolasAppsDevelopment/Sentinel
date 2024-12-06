<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quizz;
use App\Entity\User;
use App\Form\QuizFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

// TODO: add #[Route(path: '/quiz')] without breaking the redirections
class QuizController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(path: '/', name: 'app_quiz_view_all')]
    public function viewAll(Request $request): Response
    {
        $trendQuiz = $this->entityManager->getRepository(Quizz::class)->getTrendQuizzes();
        $lastQuiz = $this->entityManager->getRepository(Quizz::class)->getLastQuizzes();
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
            $searchedQuiz = $this->entityManager->getRepository(Quizz::class)->findByTitle($data['query']);
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
        $quiz = $this->entityManager->getRepository(Quizz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        return $this->render('quiz/view.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route(path: 'quiz/play/{id}', name: 'app_quiz_play')]
    public function play(string $id): Response
    {
        $quiz = $this->entityManager->getRepository(Quizz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        // TODO: add new attempt

        // get first question and view it
        return $this->render('question/view.html.twig', [
            'question' => $quiz->getQuestions()[0],
        ]);
    }

    #[Route(path: 'quiz/add', name: 'app_quiz_add')]
    public function add(Request $request, UserInterface $user, SluggerInterface $slugger,): Response
    {
        $userInDB = $this->getUser();
        if (!$userInDB) {
            return new Response("Not authorized", 401);
        }

        $form = $this->createForm(QuizFormType::class, new Quizz());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quiz = $form->getData();
            $quiz->setAuthor($userInDB);
            $quiz->setCreatedDate(new \DateTime());

            for ($questionIndex = 0; $questionIndex < $quiz->getQuestions()->count(); $questionIndex++) {
                $question = $quiz->getQuestions()[$questionIndex];
                $question->setQuizz($quiz);

                $question->getAnswer1()->setQuestion($question);
                $question->getAnswer2()->setQuestion($question);

                if ($question->getAnswer3()) {
                    $question->getAnswer3()->setQuestion($question);
                }
                if ($question->getAnswer4()) {
                    $question->getAnswer4()->setQuestion($question);
                }

                $fileType = $form->get('questions')[$questionIndex]['attachement']->getData()->getMimeType();
                if (str_contains($fileType, 'image')) {
                    $question->setType(1);
                } elseif (str_contains($fileType, 'audio')) {
                    $question->setType(2);
                } else {
                    $question->setType(0);
                }

                $ressourceFile = $form->get('questions')[$questionIndex]['attachement']->getData();
                if ($ressourceFile) {
                    $originalFilename = pathinfo($ressourceFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$ressourceFile->guessExtension();
                    try {
                        $ressourceFile->move("uploads", $newFilename);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $question->setRessourceFilename($newFilename);
                }

                $quiz->addQuestion($question);
            }

            $this->entityManager->persist($quiz);
            $this->entityManager->flush();
        }

        // just display add page, save logic in /quiz/save !
        return $this->render('quiz/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: 'quiz/remove/{id}', name: 'app_quiz_remove')]
    public function remove(string $id, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quizz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            return new Response("Not authorized", 401);
        }

        return $this->render('quiz/index.html.twig');
    }

    #[Route(path: 'quiz/edit/{id}', name: 'app_quiz_edit')]
    public function edit(string $id, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quizz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            return new Response("Not authorized", 401);
        }

        // just display edit page, save logic in /quiz/save !
        return $this->render('quiz/edit.html.twig');
    }

    #[Route(path: 'quiz/save/{id}', name: 'app_quiz_save')]
    public function save(string $id, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quizz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            return new Response("Not authorized", 401);
        }

        // TODO: redirect to right quiz id view
        return $this->render('quiz/view.html.twig');
    }

    #[Route(path: 'quiz/{id}/add/question', name: 'app_question_add')]
    public function addQuestion(string $id, UserInterface $user): Response
    {
        $quiz = $this->entityManager->getRepository(Quizz::class)->findOneBy(['id' => $id]);
        if (!$quiz) {
            return new Response("Not found", 404);
        }

        $userInDB = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        if (!$userInDB || $quiz->getAuthor()->getId() !== $userInDB->getId()) {
            return new Response("Not authorized", 401);
        }

        // just display add page, save logic in /question/save !
        return $this->render('question/add.html.twig');
    }
}
