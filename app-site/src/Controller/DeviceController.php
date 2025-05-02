<?php

namespace App\Controller;

use App\Dto\DiscoverDeviceDto;
use App\Dto\TriggeredDeviceDto;
use App\Entity\Detection;
use App\Entity\Device;
use App\Service\ApiResponseService;
use App\Service\CoupleService;
use App\Service\ImageManagerService;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DeviceService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class DeviceController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService,
        private readonly DeviceService $deviceService,
        private readonly CoupleService $coupleService,
        private readonly ImageManagerService $imageManagerService,
        private readonly SettingService $settingService,
    ) {}

    #[Route('/devices/discover', name: 'app_devices_discover', methods: 'POST')]
    public function discoverDevice(#[MapRequestPayload] DiscoverDeviceDto $deviceDto): Response
    {
        // Check if the device already exists
        $deviceRepository = $this->entityManager->getRepository(Device::class);

        $deviceWithSameIp = $deviceRepository->findOneBy(["ip" => $deviceDto->ip]);
        if ($deviceWithSameIp) {
            $this->entityManager->remove($deviceWithSameIp);
        }

        $device = $deviceRepository->findOneBy(["macAddress" => $deviceDto->mac]);
        if ($device) {
            $device->setIp($deviceDto->ip);
        } else {
            $device = new Device();
            $device->setIp($deviceDto->ip);
            $device->setMacAddress($deviceDto->mac);
            $device->setIsCamera($deviceDto->type === 'camera');
            $device->setIsPaired(false);
        }

        $this->entityManager->persist($device);
        $this->entityManager->flush();

        return $this->apiResponseService->okRaw($device->getId());
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/devices/triggered', name: 'action_device_trigger', methods: 'POST')]
    public function deviceTriggered(#[MapRequestPayload] TriggeredDeviceDto $deviceDto, EntityManagerInterface $entityManager): Response
    {
        $device = $this->deviceService->getDeviceByIpAndMac($deviceDto->ip, $deviceDto->mac);
        if (!$device) {
            return $this->apiResponseService->error('Failed to retrieve device.');
        }

        $couple = $this->coupleService->getCoupleByActionId($device->getId());
        if (!$couple) {
            return $this->apiResponseService->error('Failed to retrieve couple.');
        }

        if (!$couple->isEnabled() || $this->settingService->isInsideDeactivationRange($couple->getUser()->getId())) {
            return $this->apiResponseService->error('Couple is disabled.');
        }

        $filename = $this->imageManagerService->saveDetectionImage($couple->getCameraDevice()->getIp());

        $detection = new Detection();
        $detection->setCouple($couple);
        $detection->setImageFilename($filename);

        $userSettings = $this->settingService->getSettingByUser($couple->getUser()->getId());

        if ($userSettings->isSendMail() && ($userSettings->getLastEmailSentAt() ?? 0) < new \DateTime('-1 hour')) {
            $userMail = $this->getUser()->getEmail();

            // send email to user in controller
            $headers = "From: Sentinel <noreply@sentinel.fr>" . "\r\n" .
                "MIME-Version: 1.0\r\n" .
                "Content-Type: text/html; charset=UTF-8\r\n" .
                "X-Mailer: PHP/" . phpversion();
            $send = mail(
                $userMail,
                "Mouvement détecté sur votre caméra !",
                "
                <h1>Mouvement détecté sur votre caméra</h1>
                <p>Nous avons détecté un mouvement sur votre caméra " . $couple->getTitle() . " à " . $detection->getTriggeredAt()->format("d/m/Y H:i") . ".</p>
                <p>Pour plus d'informations, veuillez vous connecter à votre compte.</p>
                ",
                $headers
            );

            if ($send) {
                $userSettings->setLastEmailSentAt($userSettings->getId());
                $entityManager->persist($userSettings);
            }
        }

        $entityManager->persist($detection);
        $entityManager->flush();

        return $this->apiResponseService->ok(null);
    }
}
