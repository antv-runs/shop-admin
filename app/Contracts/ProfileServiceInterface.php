<?php

namespace App\Contracts;

use App\Models\User;
use App\DTOs\UpdateUserProfileDTO;
use App\Exceptions\BusinessException;

interface ProfileServiceInterface
{
    /**
     * Update user profile
     *
     * @return User
     * @throws BusinessException
     */
    public function updateProfile(User $user, UpdateUserProfileDTO $dto);

    /**
     * Delete user's profile image
     *
     * @return User
     */
    public function deleteProfileImage(User $user);
}
