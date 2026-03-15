<?php

namespace App\Contracts;

use App\Models\Category;
use App\DTOs\CreateCategoryDTO;
use App\DTOs\UpdateCategoryDTO;
use App\DTOs\CategoryFilterDTO;

interface CategoryServiceInterface
{
    /**
     * Get all categories
     */
    public function getAllCategories(CategoryFilterDTO $filter);

    /**
     * Get category by ID
     */
    public function getCategory($id);

    /**
     * Create a new category
     */
    public function createCategory(CreateCategoryDTO $dto);

    /**
     * Update category
     */
    public function updateCategory(Category $category, UpdateCategoryDTO $dto);

    /**
     * Delete category (soft delete)
     */
    public function deleteCategory($id);

    /**
     * Get trashed categories
     */
    public function getTrashed($perPage = 15);

    /**
     * Restore category
     */
    public function restoreCategory($id);

    /**
     * Force delete category
     */
    public function forceDeleteCategory($id);

    /**
     * Get categories for storefront navigation
     */
    public function getCategoriesForStore(CategoryFilterDTO $filter);
}
