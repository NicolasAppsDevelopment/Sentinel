<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class ImageManagerService {
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {}

    public function saveDetectionImage(string $cameraIp): string
    {
        $destFolderPath = $this->parameterBag->get('detections_dir');
        $destFilename = uniqid() . '.jpg';
        $image = imagecreatefromjpeg('http://' . $cameraIp . '/capture');
        file_put_contents($destFolderPath . '/' . $destFilename, $image);
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
