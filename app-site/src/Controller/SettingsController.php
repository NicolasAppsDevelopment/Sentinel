<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingFormType;
use App\Service\DetectionService;
use App\Service\SettingService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class SettingsController extends AbstractController
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly EntityManagerInterface $entityManager,
        private readonly DetectionService $detectionService,
    ) {}

    #[Route('/settings', name: 'app_settings')]
    public function index(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to edit settings! Sign in first!');
            return $this->redirectToRoute('app_login');
        }

        $setting = $this->settingService->getSettingByUser($user->getId()) ?? new Setting();
        // TODO: set access point name here like this \/
        //$setting->setAccessPointName($this->settingService->getAccessPointName());

        $settingsForm = $this->createForm(SettingFormType::class, $setting);
        $settingsForm->handleRequest($request);

        if ($settingsForm->isSubmitted() && $settingsForm->isValid()) {
            return $this->saveSettingForm($settingsForm, $user);
        }

        $errors = [];
        foreach ($settingsForm->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());
            $errors[] = $error->getMessage();
        }

        return $this->render('settings/view.html.twig', [
            'errors' => $errors,
            'settingsForm' => $settingsForm
        ]);
    }

    public function saveSettingForm(FormInterface $form, UserInterface $userInDB): RedirectResponse | Response
    {
        $setting = $form->getData();

        if (!$setting) {
            $this->addFlash('error', 'Settings not found');
            return $this->redirectToRoute('app_settings');
        }

        if (!$this->settingService->setAccessPointConfig($setting->getAccessPointName(), $setting->getAccessPointPassword())) {
            $this->addFlash('error', 'Failed to set access point configuration');
            return $this->redirectToRoute('app_settings');
        }

        $setting->setUser($userInDB);
        $setting->setLastEmailSentAt(null);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();

        $this->addFlash('success', 'Settings saved successfully!');
        return $this->redirectToRoute('app_settings');
    }

    #[Route('/settings/detections/delete', name: 'user_detections_delete')]
    public function deleteAllUserDetection(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You need to sign in to remove all detections of a user !');
            return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
        }

        $userId = $user->getId();
        $this->detectionService->deleteAllDetectionsByUser($userId);

        $this->addFlash('success', 'All your detections have been deleted successfully !');
        return $this->redirectToRoute('app_settings', [], Response::HTTP_SEE_OTHER);
    }
}
