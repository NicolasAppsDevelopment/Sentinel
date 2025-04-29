<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        $serverTime = (new DateTime())->format('Y-m-d H:i:s');

        return $this->render('settings/view.html.twig', [
            'serverTime' => $serverTime
        ]);
    }

    #[Route('/settings/sync-time', name: 'app_settings_sync_time', methods: ['POST'])]
    public function syncTime(Request $request): Response
    {
        // Validate the date format (Y-m-d H:i:s)
        $datetime = $request->request->get('server-time'); 
        if ($datetime) {
            // Formater correctement la date pour la commande `date`
            $dateFormatted = date('Y-m-d H:i:s', strtotime($datetime));
            $process = new Process(['sudo', 'date', '-s', $dateFormatted]);
            $process->run();



        if (!$process->isSuccessful()) {
            return new Response('Erreur lors de la synchronisation de l\'heure : ' . $process->getErrorOutput(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('Heure synchronisée avec succès.');
        }  
        return new Response('Date invalide.');

    }
    #[Route('/settings/server-time', name: 'app_settings_server_time')]
    public function serverTime(): Response
    {
        $serverTime = (new DateTime())->format('Y-m-d H:i:s');

        return $this->render('settings/inputserver.html.twig', [
            'serverTime' => $serverTime,
        ]);
    }
}
