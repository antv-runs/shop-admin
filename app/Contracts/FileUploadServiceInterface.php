<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * File Upload Service Contract
 *
 * Dependency Inversion Principle: depend on abstraction, not concrete class
 * Allows easy implementation swapping (local storage, cloud storage, etc)
 */
interface FileUploadServiceInterface
{
    /**
     * Upload a product image and return the path
     */
    public function uploadProductImage(UploadedFile $file): string;

    /**
     * Upload a profile image and return the path
     */
    public function uploadProfileImage(UploadedFile $file): string;

    /**
     * Delete a file from storage
     */
    public function deleteFile(string $path): void;

    /**
     * Replace old file with new one
     */
    public function replaceFile(?string $oldPath, UploadedFile $newFile): string;

    /**
     * Check if a file exists in storage
     */
    public function fileExists(string $path): bool;

    /**
     * Write content to a file in storage
     */
    public function putContent(string $path, string $content): string;

    /**
     * Create a directory in storage
     */
    public function makeDirectory(string $path): void;

    /**
     * Get URL for a file in storage
     */
    public function getUrl(string $path, string $disk = 'minio'): string;

    /**
     * Get last modified timestamp of a file
     */
    public function getLastModified(string $path): int;

    /**
     * Download a file from storage
     */
    public function download(string $path, string $filename, array $headers = []): StreamedResponse;
}
