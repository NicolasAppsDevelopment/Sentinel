<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class FileManagerService {
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {}

    public function uploadReplaceFile(?UploadedFile $file, ?string $oldFilename): ?string
    {
        if (!$file) {
            return $oldFilename;
        }
        $this->removeFile($oldFilename);
        return $this->uploadFile($file);
    }

    public function uploadFile(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        $newFilename = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->parameterBag->get('uploads_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('An error occurred while uploading the file');
        }

        return $newFilename;
    }

    public function getFileType(?UploadedFile $file, ?int $oldType): int
    {
        if (!$file) {
            return $oldType ?? 0;
        }

        $fileType = $file->getMimeType();
        if (str_contains($fileType, 'image')) {
            return 1;
        } elseif (str_contains($fileType, 'audio')) {
            return 2;
        } elseif (str_contains($fileType, 'video')) {
            return 3;
        } else {
            return 0;
        }
    }

    public function removeFile(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $ressourceFilename = $this->parameterBag->get('uploads_directory') . '/' . $filename;
        if ($ressourceFilename && is_file($ressourceFilename)) {
            unlink($ressourceFilename);
        }
    }
}
