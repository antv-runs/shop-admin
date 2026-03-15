<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use App\Enums\ItemStatus;
use App\DTOs\CategoryFilterDTO;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Find a category by ID
     */
    public function findById($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Get all categories with optional filters
     */
    public function getAll(CategoryFilterDTO $filter)
    {
        $status = $filter->status ?? ItemStatus::ACTIVE->value;

        if ($status === ItemStatus::DELETED->value) {
            $query = Category::onlyTrashed();
        } elseif ($status === ItemStatus::ALL->value) {
            $query = Category::withTrashed();
        } else {
            $query = Category::query();
        }

        // Search by name
        if (!empty($filter->search)) {
            $query->where('name', 'like', "%{$filter->search}%");
        }

        // Sort (using default sort)
        $sortBy = 'id';
        $sortOrder = 'desc';

        if (in_array($sortBy, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    /**
     * Create a new category
     */
    public function create(array $data)
    {
        return Category::create($data);
    }

    /**
     * Update a category
     */
    public function update($category, array $data)
    {
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category (soft delete)
     */
    public function delete($id)
    {
        $category = $this->findById($id);
        $category->delete();
        return true;
    }

    /**
     * Get trashed categories
     */
    public function getTrashed($perPage = 15)
    {
        return Category::onlyTrashed()->latest('deleted_at')->paginate($perPage);
    }

    /**
     * Restore a category
     */
    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        if (!$category->trashed()) {
            throw new \Exception('Category is not deleted');
        }

        $category->restore();
        return $category;
    }

    /**
     * Force delete a category
     */
    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();
    }

    /**
     * Get all categories without filters
     */
    public function getAllActive()
    {
        return Category::all();
    }

    /**
     * Check if slug exists
     */
    public function slugExists($slug, $excludeId = null)
    {
        $query = Category::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    /**
     * Paginate categories
     */
    public function paginate($perPage = 15)
    {
        return Category::paginate($perPage);
    }

    /**
     * Get categories for storefront navigation
     */
    public function getPublicCategories(CategoryFilterDTO $filter)
    {
        $query = Category::query()
            ->withCount('children')
            ->when($filter->search, function ($q) use ($filter) {
                $q->where('name', 'like', "%{$filter->search}%");
            })
            ->when($filter->parentId !== null, function ($q) use ($filter) {
                $q->where('parent_id', $filter->parentId);
            })
            ->when($filter->hasChildren === true, function ($q) {
                $q->having('children_count', '>', 0);
            })
            ->when($filter->hasChildren === false, function ($q) {
                $q->having('children_count', '=', 0);
            });

        if ($filter->sort === 'created_at') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }
}
