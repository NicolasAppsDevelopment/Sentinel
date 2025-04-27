<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\JsonResponse;


final class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
        ]);
    }

    #[Route('/settings/sync-time', name: 'app_settings_sync_time')]
    public function syncTime(): Response
    {
        $dateServeur = new \DateTime();
        $dateClient = new \DateTime();


        $process = new Process(['sudo', 'date', '-s', 'true']);
        $process->run();

        if (!$process->isSuccessful()) {
            return new Response('Erreur lors de la synchronisation de l\'heure : ' . $process->getErrorOutput(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('Heure synchronisée avec succès.');
    }


    #[Route('/settings/server-time', name: 'app_settings_server_time')]

    public function serverTime(): JsonResponse
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        return new JsonResponse([
            'serverTime' => $now->format('Y-m-d H:i:s'),
        ]);
    }
}
