<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\DTOs\CreateCategoryDTO;
use App\DTOs\UpdateCategoryDTO;
use App\Models\Category;
use App\Helpers\CacheHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CategoryService implements CategoryServiceInterface
{
    private const CACHE_TTL = 300; // 5 minutes
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories
     * Cached with TTL of 300 seconds
     */
    public function getAllCategories(\Illuminate\Http\Request $request, $perPage = 15)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);

        $cacheKey = "categories:list:{$page}:{$perPage}:{$search}";

        return CacheHelper::remember($cacheKey, self::CACHE_TTL, function () use ($request, $perPage) {
            return $this->categoryRepository->getAll($request, $perPage);
        });
    }

    /**
     * Get category by ID
     * Cached with TTL of 300 seconds
     */
    public function getCategory($id)
    {
        $cacheKey = "categories:detail:{$id}";

        return CacheHelper::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return $this->categoryRepository->findById($id);
        });
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

        $result = $this->categoryRepository->create($data);

        // Invalidate list cache
        $this->invalidateCategoryListCache();

        return $result;
    }

    /**
     * Update category
     */
    public function updateCategory(Category $category, UpdateCategoryDTO $dto)
    {
        $data = $dto->toArray();
        $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);

        $result = $this->categoryRepository->update($category, $data);

        // Invalidate caches
        CacheHelper::forget("categories:detail:{$category->id}");
        $this->invalidateCategoryListCache();

        return $result;
    }

    /**
     * Delete category (soft delete)
     */
    public function deleteCategory($id)
    {
        $result = $this->categoryRepository->delete($id);

        // Invalidate caches
        CacheHelper::forget("categories:detail:{$id}");
        $this->invalidateCategoryListCache();

        return $result;
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
        $result = $this->categoryRepository->restore($id);

        // Invalidate list cache
        $this->invalidateCategoryListCache();

        return $result;
    }

    /**
     * Force delete category
     */
    public function forceDeleteCategory($id)
    {
        $result = $this->categoryRepository->forceDelete($id);

        // Invalidate caches
        CacheHelper::forget("categories:detail:{$id}");
        $this->invalidateCategoryListCache();

        return $result;
    }

    /**
     * Invalidate all category list caches
     */
    private function invalidateCategoryListCache()
    {
        CacheHelper::flushTags(['categories:list']);
    }
}
