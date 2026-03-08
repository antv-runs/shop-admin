<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use App\Enums\ItemStatus;

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
    public function getAll($request, $perPage = 15)
    {
        $perPage = (int)$request->input('per_page', $perPage);

        $status = $request->input('status', ItemStatus::ACTIVE->value);

        if ($status === ItemStatus::DELETED->value) {
            $query = Category::onlyTrashed();
        } elseif ($status === ItemStatus::ALL->value) {
            $query = Category::withTrashed();
        } else {
            $query = Category::query();
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
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
}
