<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class ImageManagerService {
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {}

    /**
     * @throws Exception
     */
    public function saveDetectionImage(string $cameraIp): string
    {
        $destFolderPath = $this->parameterBag->get('detections_dir');
        $destFilename = uniqid() . '.jpg';
        $image = imagecreatefromjpeg('http://' . $cameraIp . '/capture');

        if (!$image) {
            throw new Exception('Failed to create image from camera capture.');
        }

        $path = $destFolderPath . '/' . $destFilename;
        $success = file_put_contents($path, $image);

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
