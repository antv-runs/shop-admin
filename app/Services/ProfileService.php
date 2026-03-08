<?php

namespace App\Services;

use App\Contracts\ProfileServiceInterface;
use App\Contracts\FileUploadServiceInterface;
use App\DTOs\UpdateUserProfileDTO;

class ProfileService implements ProfileServiceInterface
{
    public function __construct(
        private FileUploadServiceInterface $fileUploadService
    ) {}

    /**
     * Update user profile information, managing image if present.
     *
     * @return \App\Models\User
     */
    public function updateProfile($user, UpdateUserProfileDTO $dto)
    {
        $data = $dto->toArray();

        // Handle image upload
        if (isset($data['profile_image']) && $data['profile_image']) {
            // Delete old image if exists
            if ($user->profile_image) {
                $this->fileUploadService->deleteFile($user->profile_image);
            }

            // Upload new profile image
            $data['profile_image'] = $this->fileUploadService->uploadProfileImage($data['profile_image']);
        } else {
            // Remove the key if not set
            unset($data['profile_image']);
        }

        $user->update($data);

        return $user;
    }

    /**
     * Delete user's profile image and return updated user.
     */
    public function deleteProfileImage($user)
    {
        if ($user->profile_image) {
            $this->fileUploadService->deleteFile($user->profile_image);
        }

        $user->update(['profile_image' => null]);

        return $user;
    }
}
