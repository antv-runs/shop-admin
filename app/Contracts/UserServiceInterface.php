<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;

interface UserServiceInterface
{
    /**
     * Build query with search and filters
     */
    public function buildQuery(Request $request);

    /**
     * Get paginated users data with metadata
     */
    public function getListData(Request $request);

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
    public function getTrashed(Request $request);

    /**
     * Restore user
     */
    public function restoreUser($id);

    /**
     * Force delete user
     */
    public function forceDeleteUser($id);
}
