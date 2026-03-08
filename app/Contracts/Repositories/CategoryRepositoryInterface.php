<?php

namespace App\Contracts\Repositories;

interface CategoryRepositoryInterface
{
    /**
     * Find a category by ID
     */
    public function findById($id);

    /**
     * Get all categories with optional filters
     */
    public function getAll($request, $perPage = 15);

    /**
     * Create a new category
     */
    public function create(array $data);

    /**
     * Update a category
     */
    public function update($category, array $data);

    /**
     * Delete a category (soft delete)
     */
    public function delete($id);

    /**
     * Get trashed categories
     */
    public function getTrashed($perPage = 15);

    /**
     * Restore a category
     */
    public function restore($id);

    /**
     * Force delete a category
     */
    public function forceDelete($id);

    /**
     * Get all categories without filters
     */
    public function getAllActive();

    /**
     * Check if slug exists
     */
    public function slugExists($slug, $excludeId = null);

    /**
     * Paginate categories
     */
    public function paginate($perPage = 15);
}
