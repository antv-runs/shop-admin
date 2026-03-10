<?php

namespace App\Services;

use App\Contracts\FileUploadServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Handle file upload operations
 *
 * Single Responsibility: only handles file uploads and storage access
 * Open/Closed: easily extended for different upload types
 * Dependency Inversion: implements FileUploadServiceInterface
 */
class FileUploadService implements FileUploadServiceInterface
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    /**
     * Upload a product image and return the storage path.
     *
     * Uses configured disk (which may be "s3" in production).  The
     * file is stored under the `products/` directory and is marked as
     * publicly visible.  The returned value is the relative path that
     * can be stored in the database.
     */
    public function uploadProductImage(UploadedFile $file): string
    {
        // putFile automatically generates a unique name (hashing the
        // original filename).  We explicitly request `public` visibility
        // to ensure S3 objects are accessible.
        return Storage::disk($this->disk)->putFile('products', $file);
    }

    /**
     * Upload a profile image and return the path
     */
    public function uploadProfileImage(UploadedFile $file): string
    {
        return $file->store('profile-images', $this->disk);
    }

    /**
     * Delete a file from public storage
     */
    public function deleteFile(string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            try {
                Storage::disk($this->disk)->delete($path);
            } catch (\Throwable $e) {
                // Log or handle silently
            }
        }
    }

    /**
     * Replace old file with new one
     */
    public function replaceFile(?string $oldPath, UploadedFile $newFile): string
    {
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }
        return $this->uploadProductImage($newFile);
    }

    /**
     * Check if a file exists in storage
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Write content to a file in storage
     */
    public function putContent(string $path, string $content): string
    {
        Storage::disk($this->disk)->put($path, $content);
        return $path;
    }

    /**
     * Create a directory in storage
     */
    public function makeDirectory(string $path): void
    {
        if (!Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->makeDirectory($path);
        }
    }

    /**
     * Get URL for a file in storage
     * Uses the configured default filesystem disk
     */
    public function getUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Get last modified timestamp of a file
     */
    public function getLastModified(string $path): int
    {
        return Storage::disk($this->disk)->lastModified($path);
    }

    /**
     * Download a file from storage
     */
    public function download(string $path, string $filename, array $headers = []): StreamedResponse
    {
        return Storage::disk($this->disk)->download($path, $filename, $headers);
    }
}
