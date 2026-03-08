<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\DTOs\CreateCategoryDTO;
use App\DTOs\UpdateCategoryDTO;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories
     */
    public function getAllCategories(\Illuminate\Http\Request $request, $perPage = 15)
    {
        return $this->categoryRepository->getAll($request, $perPage);
    }

    /**
     * Get category by ID
     */
    public function getCategory($id)
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while ($this->categoryRepository->slugExists($slug, $excludeId)) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Create a new category
     */
    public function createCategory(CreateCategoryDTO $dto)
    {
        $data = $dto->toArray();
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        return $this->categoryRepository->create($data);
    }

    /**
     * Update category
     */
    public function updateCategory(Category $category, UpdateCategoryDTO $dto)
    {
        $data = $dto->toArray();
        $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        return $this->categoryRepository->update($category, $data);
    }

    /**
     * Delete category (soft delete)
     */
    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }

    /**
     * Get trashed categories
     */
    public function getTrashed($perPage = 15)
    {
        return $this->categoryRepository->getTrashed($perPage);
    }

    /**
     * Restore category
     */
    public function restoreCategory($id)
    {
        return $this->categoryRepository->restore($id);
    }

    /**
     * Force delete category
     */
    public function forceDeleteCategory($id)
    {
        return $this->categoryRepository->forceDelete($id);
    }
}
