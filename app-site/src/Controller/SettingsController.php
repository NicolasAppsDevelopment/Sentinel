<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Entity\Setting;
use App\Form\AccessPointFormType;
use App\Form\CoupleFormType;
use App\Form\SettingFormType;
use App\Service\DetectionService;
use App\Service\SettingService;
use App\Service\UserService;
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
        private readonly UserService $userService,
        private readonly DetectionService $detectionService,
    ) {}

    #[Route('/settings', name: 'app_settings')]
    public function index(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to edit settings! Sign in first!');
            return $this->redirectToRoute('app_login');
        }

        // Create and handle settings form
        $setting = $this->settingService->getSettingByUser($user->getId()) ?? new Setting();
        $deactivationRangeForm = $this->createForm(SettingFormType::class, $setting);
        $deactivationRangeForm->handleRequest($request);

        if ($deactivationRangeForm->isSubmitted() && $deactivationRangeForm->isValid()) {
            return $this->saveSettingForm($deactivationRangeForm, $user);
        }

        $errors = [];
        foreach ($deactivationRangeForm->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        // Create and handle Access Point form
        $accessPointForm = $this->createForm(AccessPointFormType::class);
        $accessPointForm->handleRequest($request);

        if ($accessPointForm->isSubmitted() && $accessPointForm->isValid()) {
            return $this->saveAccessPointConfigForm($accessPointForm, $user);
        }

        $errors = [];
        foreach ($accessPointForm->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        $serverTime = (new DateTime())->format('Y-m-d H:i');

        return $this->render('settings/view.html.twig', [
            'serverTime' => $serverTime,
            'errors' => $errors,
            'deactivationRangeForm' => $deactivationRangeForm,
            'accessPointForm' => $accessPointForm,
        ]);
    }

    #[Route('/settings/sync-time', name: 'app_settings_sync_time', methods: ['POST'])]
    public function syncTime(Request $request): Response
    {
        $datetime = $request->request->get('server-time');
        if ($datetime) {
            $dateFormatted = date('Y-m-d H:i:s', strtotime($datetime));
            // Construire la commande shell
            $cmd = sprintf('sudo date -s "%s"', escapeshellcmd($dateFormatted));

            // Exécuter la commande (attention à la sécurité)
            $output = shell_exec($cmd);
            dd($output);

            if (!$output) {
                return new Response('Erreur lors de la synchronisation de l\'heure .', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->render('settings/form_view.html.twig', [
                    'serverTime' => $datetime,
            ]);
        }
        return new Response('Date invalide.');

    }

    #[Route('/settings/server-time', name: 'settings_server_time')]
    public function serverTime(): Response
    {
        $serverTime = (new DateTime())->format('Y-m-d H:i:s');

        return $this->render('settings/inputserver.html.twig', [
            'serverTime' => $serverTime,
        ]);
    }

    #[Route('/settings/deactivation-range/update', name: 'settings_deactivation_range_update', methods: ['POST'])]
    public function updateDeactivationRange(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to add an alarm! Sign in first!');
            return $this->redirectToRoute('app_login');
        }

        $setting = new Setting();
        $form = $this->createForm(SettingFormType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveSettingForm($form, $user);
        }

        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        return $this->redirectToRoute('app_settings');
    }

    public function saveSettingForm(FormInterface $form, UserInterface $userInDB): RedirectResponse | Response
    {
        $setting = $form->getData();

        if (!$setting) {
            $this->addFlash('error', 'Settings not found');
            return $this->redirectToRoute('app_settings');
        }

        $setting->setUser($userInDB);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();

        $this->addFlash('success', 'Settings saved successfully!');
        return $this->redirectToRoute('app_settings');
    }

    #[Route('/settings/access-point/update', name: 'access_point_update', methods: ['POST'])]
    public function updateAccessPoint(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to edit the access point! Sign in first!');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AccessPointFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveAccessPointConfigForm($form, $user);
        }

        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        return $this->redirectToRoute('app_settings');
    }

    public function saveAccessPointConfigForm(FormInterface $form, UserInterface $userInDB): RedirectResponse | Response
    {
        $accessPointConfig = $form->getData();

        if (!$accessPointConfig) {
            $this->addFlash('error', 'Settings not found');
            return $this->redirectToRoute('app_settings');
        }

        // Check authorization
        if (!$userInDB) {
            $this->addFlash('error', 'You need to sign in to edit the access point !');
            return $this->redirectToRoute('app_login');
        }

        $ssid     = trim($accessPointConfig['accessPointName']);
        $password = trim($accessPointConfig['accessPointPassword']);

        if (!$ssid and !$password){
            $this->addFlash('error', 'You need to define a new password and/or name to update the access point !');
            return $this->redirectToRoute('app_settings');
        }

        $configPath = '/etc/hostapd/hostapd.conf';

        // Load existing access point config
        $fileContent = @file_get_contents($configPath);
        if ($fileContent === false) {
            return new Response(
                'Failed to read the access point configuration.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($password){
            if ($password != $accessPointConfig['accessPointPasswordVerify']) {
                $this->addFlash('error', 'The password and confirmation password must be the same');
                return $this->redirectToRoute('app_settings');
            }

            $fileContent = preg_replace('/^wpa_passphrase=.*$/m', "wpa_passphrase={$password}", $fileContent);

        }

        if ($ssid){
            $fileContent = preg_replace('/^ssid=.*$/m', "ssid={$ssid}", $fileContent);
        }


        if ($fileContent === false) {
            return new Response('Error processing the access point configuration.',Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Modify hostapd.conf
        if (!@file_put_contents($configPath, $fileContent)) {
            return new Response('Failed to modify the access point configuration.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Reload hostapd
        $output     = [];
        $returnCode = 0;
        exec('sudo /usr/local/bin/reload-hostapd.sh 2>&1', $output, $returnCode);

        if (0 !== $returnCode) {
            return new Response(
                'Failed to reload hostapd: ' . implode("\n", $output),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $this->addFlash('success', 'Access point configuration saved successfully!');
        return $this->redirectToRoute('app_settings');
    }

    #[Route('/settings/detections/delete', name: 'user_detections_delete', methods: ['POST'])]
    public function deleteAllUserDetection(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You need to sign in to remove all detections of a user !');
            return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
        }

        $userId = $user->getId();
        $this->detectionService->deleteAllDetectionsByUser($userId);

        if (!empty($this->detectionService->getAllDetectionsByUser($userId))) {
            $this->addFlash('error', 'The delete have failed !');
            return $this->redirectToRoute('app_settings');
        }

        $this->addFlash('success', 'All your detections have been deleted successfully !');
        return $this->redirectToRoute('app_settings', [], Response::HTTP_SEE_OTHER);

    }
}
