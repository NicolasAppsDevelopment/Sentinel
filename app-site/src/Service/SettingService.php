<?php

namespace App\Service;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SettingService
{
    public function __construct(
        private readonly SettingRepository $settingRepository
    ) {}

    /**
     * Get settings for a given user ID.
     * @return ?Setting
     */
    public function getSettingByUser(int $userId): ?Setting
    {
        return $this->settingRepository->findByUserId($userId);
    }

    public function isInsideDeactivationRange(int $userId): bool {
        $settings = $this->getSettingByUser($userId);
        if ($settings) {
            $dayOfWeek = date('w');
            switch ($dayOfWeek) {
                case 0:
                    return $this->isCurrentTimeBetween(
                        $settings->getSundayFrom(),
                        $settings->getSundayTo()
                    );
                case 1:
                    return $this->isCurrentTimeBetween(
                        $settings->getMondayFrom(),
                        $settings->getMondayTo()
                    );
                case 2:
                    return $this->isCurrentTimeBetween(
                        $settings->getTuesdayFrom(),
                        $settings->getTuesdayTo()
                    );
                case 3:
                    return $this->isCurrentTimeBetween(
                        $settings->getWednesdayFrom(),
                        $settings->getWednesdayTo()
                    );
                case 4:
                    return $this->isCurrentTimeBetween(
                        $settings->getThursdayFrom(),
                        $settings->getThursdayTo()
                    );
                case 5:
                    return $this->isCurrentTimeBetween(
                        $settings->getFridayFrom(),
                        $settings->getFridayTo()
                    );
                case 6:
                    return $this->isCurrentTimeBetween(
                        $settings->getSaturdayFrom(),
                        $settings->getSaturdayTo()
                    );
            }
        }
        return false;
    }

    private function isCurrentTimeBetween(?DateTimeInterface $start, ?DateTimeInterface $end): bool
    {
        if (null === $start || null === $end) {
            return false;
        }

        $now = new DateTimeImmutable('now');

        $startTime = $start->format('H:i:s');
        $endTime = $end->format('H:i:s');
        $currentTime = $now->format('H:i:s');

        return $currentTime >= $startTime && $currentTime <= $endTime;
    }

    public function setAccessPointConfig(?string $ssid, ?string $password): bool
    {
        $configPath = '/etc/hostapd/hostapd.conf';

        // Load existing access point config
        $fileContent = @file_get_contents($configPath);
        if ($fileContent === false) {
            return false;
        }

        if ($password){
            $fileContent = preg_replace('/^wpa_passphrase=.*$/m', "wpa_passphrase={$password}", $fileContent);
        }
        if ($ssid){
            $fileContent = preg_replace('/^ssid=.*$/m', "ssid={$ssid}", $fileContent);
        }


        if ($fileContent === false) {
            return false;
        }

        // Modify hostapd.conf
        if (!@file_put_contents($configPath, $fileContent)) {
            return false;
        }

        // Reload hostapd
        $output     = [];
        $returnCode = 0;
        exec('sudo /usr/local/bin/reload-hostapd.sh 2>&1', $output, $returnCode);

        if (0 !== $returnCode) {
            return false;
        }

        return true;
    }

    public function setServerTime(\DateTime $newDateTime): bool
    {
        $dateFormatted = date('Y-m-d H:i:s', $newDateTime);

        // Construire la commande shell
        $cmd = sprintf('sudo date -s "%s"', escapeshellcmd($dateFormatted));

        // Exécuter la commande (attention à la sécurité)
        $output = shell_exec($cmd);

        if (!$output) {
            return false;
        }
        return true;
    }
}
