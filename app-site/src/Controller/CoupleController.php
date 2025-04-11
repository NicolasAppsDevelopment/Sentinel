<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Form\CoupleFormType;
use App\Service\CoupleService;
use App\Service\DetectionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormInterface;

final class CoupleController extends AbstractController{

    public function __construct(
        private readonly CoupleService $coupleService,
        private readonly DetectionService $detectionService,
        private readonly EntityManagerInterface $entityManager

    ) {}


    #[Route('/couples', name: 'app_couples')]
    public function index(UserInterface $user): Response
    {
        $couple = $this->coupleService->getAllCouplesByUser($user->getId());
        //dd($couple[0]->detections);
        $wifi = "good";

        //dd($couple);
        return $this->render('couple/all.html.twig', [
            'controller_name' => 'CoupleController',
            'couple'=> $couple,
            'wifi'=>$wifi,
        ]);
    }

    #[Route('/couples/add', name: 'app_couples_add')]
    public function addCouple(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to add couple! Sign in first!');
            return $this->redirectToRoute('app_couples_all');
        }

        $couple = new Couple();
        $form = $this->createForm(CoupleFormType::class, $couple);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveCoupleForm($form, $user);
        }

        // just display add page, save logic in /couple/save !
        return $this->render('couple/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/couples/view/{id}', name: 'app_couples_view')]
    public function getCoupleById(int $id): Response
    {
        $couple = $this->coupleService->getCoupleById($id);
        $detections = $this->detectionService->getAllDetectionsByCoupleId($id);

        // dd($detections);
        return $this->render('couple/view.html.twig', [
            'controller_name' => 'CoupleController',
            'coupleInfo' => $couple,
            'detections' => $detections,
        ]);
    }



    /**
     * @param FormInterface $form
     * @param UserInterface $userInDB
     * @return RedirectResponse
     */
    public function saveCoupleForm(FormInterface $form, UserInterface $userInDB): RedirectResponse
    {
        $couple = $form->getData();
        $couple->setUser($userInDB);
        $couple->setAssociationDate(new DateTime());
        $couple->setEnabled(true);

        $actionDevice = $couple->getActionDevice();
        if ($actionDevice) {
            $actionDevice->setIsPaired(true);
        }

        $cameraDevice = $couple->getCameraDevice();
        if ($cameraDevice) {
            $cameraDevice->setIsPaired(true);
        }

        $this->entityManager->persist($couple);
        $this->entityManager->flush();

        $this->addFlash('success', 'Couple saved successfully!');
        return $this->redirectToRoute('app_couples');
    }

    #[Route('/couples/enabledisable/{id}', name: 'app_couples_enabledisable')]
    public function enableDisableCouple(int $id): Response
    {
        $couple = $this->coupleService->getCoupleById($id);

        $coupleState = $this->coupleService->enableDisableCouple($id);
        // dd($detections);
        return $this->render('couple/enable-disable-button.html.twig', [
            'controller_name' => 'CoupleController',
            'coupleInfo'=> $couple,
            'coupleState'=> $coupleState,

        ]);
    }

}
