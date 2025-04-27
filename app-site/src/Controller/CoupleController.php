<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Form\CoupleFormType;
use App\Service\ApiResponseService;
use App\Service\CoupleService;
use App\Service\DetectionService;
use App\Service\DeviceService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormInterface;

final class CoupleController extends AbstractController {

    public function __construct(
        private readonly CoupleService $coupleService,
        private readonly DetectionService $detectionService,
        private readonly DeviceService $deviceService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseService $apiResponseService
    ) {}


    #[Route('/couples', name: 'app_couples')]
    public function index(UserInterface $user): Response
    {
        $couples = $this->coupleService->getAllCouplesByUserWithDetections($user->getId());
        //dd($couples);
        $wifi = "good";

        return $this->render('couple/all.html.twig', [
            'couples'=> $couples,
            'wifi'=>$wifi,
        ]);
    }

    #[Route('/couples/add', name: 'app_couples_add')]
    public function addCouple(Request $request, UserInterface $user): Response
    {
        if (!$user) {
            $this->addFlash('error', 'You are not authorized to add couple! Sign in first!');
            return $this->redirectToRoute('app_couples_all');
        }

        $couple = new Couple();
        $form = $this->createForm(CoupleFormType::class, $couple);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveCoupleForm($form, $user);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        // just display add page, save logic in /save !
        return $this->render('couple/add.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route('/couples/view/{id}', name: 'app_couples_view')]
    public function getCoupleById(string $id): Response
    {
        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            $this->addFlash('error', 'Couple not found');
            return $this->redirectToRoute('app_couples');
        }

        $coupleStatus = $this->coupleService->getStatus($couple);

        // TODO: Get user last detections today
        $detections = $this->detectionService->getAllDetectionsByCoupleId($id);

        // TODO: Get number of new detections since last seek from service

        // TODO: Get status from camera and action module



        $couple->setLastDetectionSeekDate(new DateTime());
        $this->entityManager->persist($couple);
        $this->entityManager->flush();

        return $this->render('couple/view.html.twig', [
            'coupleInfo' => $couple,
            'detections' => $detections,
            'actionDeviceSignalStrength' => $coupleStatus->actionDeviceStatus->rssiState,
            'cameraDeviceSignalStrength' => $coupleStatus->cameraDeviceStatus->rssiState,
        ]);
    }



    /**
     * @param FormInterface $form
     * @param UserInterface $userInDB
     * @return RedirectResponse
     */
    public function saveCoupleForm(FormInterface $form, UserInterface $userInDB): RedirectResponse
    {
        $couple = $form->getData();
        $couple->setUser($userInDB);
        $couple->setAssociationDate(new DateTime());
        $couple->setEnabled(true);

        $actionDevice = $couple->getActionDevice();
        if ($actionDevice) {
            $actionDevice->setIsPaired(true);
        }

        $cameraDevice = $couple->getCameraDevice();
        if ($cameraDevice) {
            $cameraDevice->setIsPaired(true);
        }

        $this->entityManager->persist($couple);
        $this->entityManager->flush();

        $this->addFlash('success', 'Couple saved successfully!');
        return $this->redirectToRoute('app_couples');
    }

    #[Route('/couples/enable-disable/{id}', name: 'app_couples_enable_disable')]
    public function enableDisableCouple(int $id): Response
    {
        $this->coupleService->enableDisableCouple($id);

        $res = $this->apiResponseService->ok(null);
        $res->headers->set('Hx-Refresh', 'true');

        return $res;
    }

    #[Route('/couples/delete/{id}', name: 'app_couples_delete')]
    public function deleteCouple(string $id): Response
    {
        $couple = $this->coupleService->getCoupleById($id);
        if ($couple !== null) {
            $couple->getActionDevice()?->setIsPaired(false);
            $couple->getCameraDevice()?->setIsPaired(false);

            $this->entityManager->remove($couple);
            $this->entityManager->flush();

            $this->addFlash('success', 'Camera deleted successfully!');
        }

        return $this->redirectToRoute('app_couples');
    }

    #[Route('/couples/{id}/stream', name: 'app_couples_stream')]
    public function getSecureStream(string $id, UserInterface $user): Response
    {
        // 1. Auth check
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access this stream! Sign in first!');
        }

        // 2. Lookup couple info from database
        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            return $this->apiResponseService->error('Couple not found');
        }
        //if ($couple->getUser() !== $user) {
        //    return $this->apiResponseService->error('Not authorized');
        //}
        $cameraDevice = $couple->getCameraDevice();
        if ($cameraDevice === null) {
            return $this->apiResponseService->error('Camera not found');
        }
        if ($cameraDevice->isPaired() === false) {
            return $this->apiResponseService->error('Camera not paired');
        }

        // 2. Send internal redirect
        $streamPath = '/protected-stream/?ip=' . $cameraDevice->getIp();

        return new Response('', 200, [
            'X-Accel-Redirect' => $streamPath,
            'Content-Type' => 'multipart/x-mixed-replace; boundary=123456789000000000000987654321',
        ]);
    }

    #[Route('/couples/{id}/stream/quality/{level}', name: 'app_couples_stream_quality')]
    public function setStreamQuality(string $id, string $level, UserInterface $user): Response
    {
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access capture route! Sign in first!');
        }

        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            return $this->apiResponseService->error('Couple not found');
        }
//            if ($couple->getUser() !== $user) {
//                return $this->apiResponseService->error('Not authorized');
//            }

        $cameraDevice = $couple->getCameraDevice();
        if ($cameraDevice === null) {
            return $this->apiResponseService->error('Camera not found');
        }
        if ($cameraDevice->isPaired() === false) {
            return $this->apiResponseService->error('Camera not paired');
        }

        $client = HttpClient::create();

        try {
            $url = 'http://' . $cameraDevice->getIp() . '/control?var=framesize&val=' . $level;
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                return $this->apiResponseService->error('Unable to set stream quality: ' . $response->getStatusCode());
            }

            return new StreamedResponse(function () use ($response) {
                echo $response->getContent();
            }, 200);
        } catch (\Exception $e) {
            return $this->apiResponseService->error('Unable set stream quality: ' . $e->getMessage());
        }
    }

    #[Route('/couples/{id}/capture', name: 'app_couples_capture', methods: 'GET')]
    public function getSecureCapture(string $id, UserInterface $user): Response
    {
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access capture route! Sign in first!');
        }

        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            return $this->apiResponseService->error('Couple not found');
        }
//            if ($couple->getUser() !== $user) {
//                return $this->apiResponseService->error('Not authorized');
//            }

        $cameraDevice = $couple->getCameraDevice();
        if ($cameraDevice === null) {
            return $this->apiResponseService->error('Camera not found');
        }
        if ($cameraDevice->isPaired() === false) {
            return $this->apiResponseService->error('Camera not paired');
        }

        $client = HttpClient::create();

        try {//TODO Extract into a specific file
            $url = 'http://' . $cameraDevice->getIp() . '/capture?_cb=1744097322029';
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                return $this->apiResponseService->error('Unable to capture image: ' . $response->getStatusCode());
            }

            $contentType = $response->getHeaders()['content-type'][0] ?? 'image/jpg';

            return new StreamedResponse(function () use ($response) {
                echo $response->getContent();
            }, 200, [
                'Content-Type' => $contentType,
            ]);
        } catch (\Exception $e) {
            return $this->apiResponseService->error('Unable fetching image: ' . $e->getMessage());
        }
    }

    #[Route('/couple/{id}/update', name: 'app_couple_update_name', methods: ['POST'])]
    public function update(Request $request, int $id): RedirectResponse
    {
        $newTitle = $request->request->get('title', '');

        $this->coupleService->updateTitle($id, $newTitle);

        return $this->redirectToRoute('app_couples_view', [
            'id' => $id,
        ]);
    }

    #[Route('/couples/{id}/enable-buzzer', name: 'app_couples_enable_buzzer')]
    public function enableBuzzer(string $id, UserInterface $user): Response
    {
        // 1. Auth check
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access this stream! Sign in first!');
        }

        // 2. Lookup couple info from database
        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            return $this->apiResponseService->error('Couple not found');
        }
        //if ($couple->getUser() !== $user) {
        //    return $this->apiResponseService->error('Not authorized');
        //}
        $actionDevice = $couple->getCameraDevice();
        if ($actionDevice === null) {
            return $this->apiResponseService->error('Action module not found');
        }
        if ($actionDevice->isPaired() === false) {
            return $this->apiResponseService->error('Action module not paired');
        }

        // 2. Send internal redirect
        $internalEnableBuzzerPath = '/protected-enable-buzzer/?ip=' . $actionDevice->getIp();

        return new Response('', 200, [
            'X-Accel-Redirect' => $internalEnableBuzzerPath,
            'Content-Type' => 'application/json',
        ]);
    }

    #[Route('/couples/{id}/disable-buzzer', name: 'app_couples_disable_buzzer')]
    public function disableBuzzer(string $id, UserInterface $user): Response
    {
        // 1. Auth check
        if (!$user) {
            return $this->apiResponseService->error('You are not authorized to access this stream! Sign in first!');
        }

        // 2. Lookup couple info from database
        $couple = $this->coupleService->getCoupleById($id);
        if ($couple === null) {
            return $this->apiResponseService->error('Couple not found');
        }
        //if ($couple->getUser() !== $user) {
        //    return $this->apiResponseService->error('Not authorized');
        //}
        $actionDevice = $couple->getCameraDevice();
        if ($actionDevice === null) {
            return $this->apiResponseService->error('Action module not found');
        }
        if ($actionDevice->isPaired() === false) {
            return $this->apiResponseService->error('Action module not paired');
        }

        // 2. Send internal redirect
        $internalEnableBuzzerPath = '/protected-disable-buzzer/?ip=' . $actionDevice->getIp();

        return new Response('', 200, [
            'X-Accel-Redirect' => $internalEnableBuzzerPath,
            'Content-Type' => 'application/json',
        ]);
    }
}
