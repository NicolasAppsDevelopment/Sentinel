<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\AccessPointFormType;
use App\Form\SettingFormType;
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
    ) {}

    #[Route('/settings', name: 'app_settings')]
    public function index(UserInterface $user): Response
    {
        $serverTime = (new DateTime())->format('Y-m-d H:i:s');

        return $this->render('settings/view.html.twig', [
            'serverTime' => $serverTime,
            'activationPlanning' => $this->settingService->getSettingByUser($user->getId()),
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

        // Check authorization
        if (!$userInDB) {
            $this->addFlash('error', 'You need to sign in to edit this alarm !');
            return $this->redirectToRoute('app_login');
        }
        if ($setting->getUser()) {
            if ($userInDB->getUserIdentifier() != $setting->getUser()->getUsername()) {
                $this->addFlash('error', 'You are not authorized to edit this settings !');
                return $this->redirectToRoute('app_settings');
            }
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

        $ssid     = $accessPoint['accessPointName'] ?? '';
        $password = $accessPoint['accessPointPassword'] ?? '';
        $configPath = '/etc/hostapd/hostapd.conf';

        // Load existing access point config
        $content = @file_get_contents($configPath);
        if (!$content) {
            return new Response(
                'Failed to read the access point configuration.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // Replace only ssid and wpa_passphrase
        $patterns = [
            '/^ssid=.*$/m',
            '/^wpa_passphrase=.*$/m',
        ];
        $replacements = [
            "ssid={$ssid}",
            "wpa_passphrase={$password}",
        ];
        $newContent = preg_replace($patterns, $replacements, $content);

        if (!$newContent) {
            return new Response('Error processing the access point configuration.',Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Modify hostapd.conf
        if (!@file_put_contents($configPath, $newContent)) {
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
}
