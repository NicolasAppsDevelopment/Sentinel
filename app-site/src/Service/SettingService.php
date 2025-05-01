<?php

namespace App\Service;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use DateTimeImmutable;
use DateTimeInterface;

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

    public function isInsideActivationPlanning(int $userId): bool {
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
        return true;
    }

    private function isCurrentTimeBetween(?DateTimeInterface $start, ?DateTimeInterface $end): bool
    {
        $now = new DateTimeImmutable('now');

        $startTime = $start ? $start->format('H:i:s') : '00:00:00.000';
        $endTime = $end ? $end->format('H:i:s') : '23:59:59.999';
        $currentTime = $now->format('H:i:s');

        return $currentTime >= $startTime && $currentTime <= $endTime;
    }
}
