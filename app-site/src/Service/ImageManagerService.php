<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class ImageManagerService {
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {}

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function saveDetectionImage(string $cameraIp): string
    {
        $destFolderPath = $this->parameterBag->get('detections_dir');
        $destFilename = uniqid() . '.jpg';
        $url = 'http://' . $cameraIp . '/capture';

        $client = HttpClient::create();

        $response = $client->request('GET', $url);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Unable to capture image, camera returned: ' . $response->getStatusCode());
        }

        $path = $destFolderPath . '/' . $destFilename;
        $success = file_put_contents($path, $response->getContent());

        if ($success === false) {
            throw new Exception('Failed to save image to destination folder: ' . $path);
        }

        return $destFilename;
    }

    public function removeDetectionImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $ressourceFilename = $this->parameterBag->get('detections_dir') . '/' . $filename;
        if ($ressourceFilename && is_file($ressourceFilename)) {
            unlink($ressourceFilename);
        }
    }
}
