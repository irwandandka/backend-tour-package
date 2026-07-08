<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class FileUploadService
{
    public function getFilePath(string $fileUrl): string
    {
        $parsedUrl = parse_url($fileUrl);

        return ltrim($parsedUrl['path'], '/');
    }

    public function uploadImage(
        $file,
        string $path = 'images',
        bool $generateWebP = false
    ): array {
        // Upload original
        $originalName = time().'_'.$file->getClientOriginalName();

        if ($path) {
            $originalPath = $path.'/'.$originalName;
        } else {
            $originalPath = $originalName;
        }

        $result = [];

        if ($generateWebP) {
            // Convert ke WebP
            $webpName = pathinfo($originalName, PATHINFO_FILENAME).'.webp';
            $webpPath = $path.'/'.$webpName;

            // Buat driver
            $manager = ImageManager::gd();

            // Build image
            $image = $manager->read($file->getRealPath())->toWebp(quality: 90);
            Storage::disk('minio')->put($webpPath, (string) $image);

            // Dapatkan URL dari file yang diunggah
            $webpUrl = Storage::disk('minio')->url($webpPath);

            $result['webp'] = $webpUrl;
            $result['webpFilename'] = $webpName;
        } else {
            Storage::disk('minio')->putFileAs($path, $file, $originalName);
            $originalUrl = Storage::disk('minio')->url($originalPath);

            $result['original'] = $originalUrl;
            $result['originalFilename'] = $originalName;
        }

        return $result;
    }

    public function uploadFile(string $filePath, string $fileDir): array
    {
        $fileName = basename($filePath);
        $storagePath = $fileDir ? ($fileDir.'/'.$fileName) : $fileName;

        // Upload file ke MinIO
        Storage::disk('minio')->put($storagePath, file_get_contents($filePath));

        return [
            'filePath' => $storagePath,
            'fileURL' => Storage::disk('minio')->url($storagePath),
        ];
    }

    public function deleteFile(string $filePath): bool
    {
        if (Storage::disk('minio')->exists($filePath)) {
            return Storage::disk('minio')->delete($filePath);
        }

        return false;
    }
}
