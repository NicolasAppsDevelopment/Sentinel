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

    public function setAccessPointConfig(?string $ssid, ?string $password): ?bool
    {
        if (!$ssid && !$password) {
            return null;
        }

        if ($password){
            if (!exec("sudo nmcli con modify hotspot ssid \"{$ssid}\"")) return false;
        }
        if ($ssid){
            if (!exec("sudo nmcli con modify hotspot ssid \"{$ssid}\"")) return false;
        }

        exec("reboot");
        return true;
    }
}
