<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

trait UploadFileTrait
{
    /**
     * disk المستخدم
     */
    protected string $disk = 'public';

    /* ================= Core ================= */

    protected function storeFile(string $folder, UploadedFile $file, ?string $filename = null): string
    {
        $name = $filename
            ? $filename . '.' . $file->getClientOriginalExtension()
            : time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($folder, $name, $this->disk);
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    /* ================= Generic ================= */

    protected function uploadImage(string $folder, UploadedFile $file): string
    {
        return $this->storeFile($folder, $file);
    }

    protected function uploadFile(string $folder, UploadedFile $file): string
    {
        return $this->storeFile($folder, $file);
    }

    /* ================= Driver Uploads ================= */

    protected function uploadDriverDocument(
        UploadedFile $file,
        string $documentType,
        int $driverId
    ): string {
        $folder = "drivers/documents/{$driverId}/{$documentType}";
        $filename = $documentType . '_' . time() . '_' . Str::random(5);

        return $this->storeFile($folder, $file, $filename);
    }

    protected function uploadIdImage(
        UploadedFile $file,
        int $driverId,
        string $side = 'front'
    ): string {
        return $this->uploadDriverDocument($file, "id_{$side}", $driverId);
    }

    protected function uploadLicenseImage(
        UploadedFile $file,
        int $driverId,
        string $side = 'front'
    ): string {
        return $this->uploadDriverDocument($file, "license_{$side}", $driverId);
    }

    protected function uploadVehicleRegistrationImage(
        UploadedFile $file,
        int $driverId
    ): string {
        return $this->uploadDriverDocument($file, 'vehicle_registration', $driverId);
    }

    protected function uploadProfilePhoto(
        UploadedFile $file,
        int $driverId
    ): string {
        $folder = "drivers/photos/{$driverId}";
        $filename = 'profile_' . time();

        return $this->storeFile($folder, $file, $filename);
    }

    /* ================= Validation ================= */

    protected function validateFileType(
        UploadedFile $file,
        array $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']
    ): bool {
        return in_array(
            strtolower($file->getClientOriginalExtension()),
            $allowedTypes
        );
    }

    protected function validateFileSize(
        UploadedFile $file,
        int $maxSizeInMB = 5
    ): bool {
        return ($file->getSize() / (1024 * 1024)) <= $maxSizeInMB;
    }
}
