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
        $datetime = $request->request->get('server-time'); 
        if ($datetime) {
            $dateFormatted = date('Y-m-d H:i:s', strtotime($datetime));
            // Construire la commande shell
            $cmd = sprintf('sudo date -s "%s"', escapeshellcmd($dateFormatted));

            // Exécuter la commande (attention à la sécurité)
            $output = shell_exec($cmd);
            dd($output);

            if (!$output) {
                return new Response('Erreur lors de la synchronisation de l\'heure.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->render('settings/form_view.html.twig', [
                    'serverTime' => $datetime,
            ]);
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
