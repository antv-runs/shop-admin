<?php

namespace App\Contracts;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\DTOs\UserFilterDTO;

interface UserServiceInterface
{
    /**
     * Get paginated users data with metadata
     */
    public function getListData(UserFilterDTO $filter);

    /**
     * Get role options
     */
    public function getRoles();

    /**
     * Create a new user
     */
    public function createUser(CreateUserDTO $dto);

    /**
     * Retrieve a single user by id (or fail).
     */
    public function getUser($id);

    /**
     * Update user by id
     */
    public function updateUser($id, UpdateUserDTO $dto);

    /**
     * Delete user (soft delete) by id
     */
    public function deleteUser($id);

    /**
     * Get trashed users
     */
    public function getTrashed(UserFilterDTO $filter);

    /**
     * Restore user
     */
    public function restoreUser($id);

    /**
     * Force delete user
     */
    public function forceDeleteUser($id);
}
