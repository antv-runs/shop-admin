<?php

namespace App\Contracts\Repositories;

use App\DTOs\UserFilterDTO;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID
     */
    public function findById($id);

    /**
     * Get all users with optional filters
     */
    public function getAll(UserFilterDTO $filter);

    /**
     * Create a new user
     */
    public function create(array $data);

    /**
     * Update a user
     */
    public function update($user, array $data);

    /**
     * Delete a user (soft delete)
     */
    public function delete($id);

    /**
     * Get trashed users
     */
    public function getTrashed(UserFilterDTO $filter);

    /**
     * Restore a user
     */
    public function restore($id);

    /**
     * Force delete a user
     */
    public function forceDelete($id);

    /**
     * Paginate users
     */
    public function paginate($perPage = 15);

    /**
     * Find user by email
     */
    public function findByEmail($email);
}
