<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Enums\ItemStatus;
use App\DTOs\ProductFilterDTO;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Find a product by ID
     */
    public function findById($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Get all products with optional filters
     */
    public function getAll(ProductFilterDTO $filter)
    {
        $status = $filter->status ?? ItemStatus::ACTIVE->value;

        if ($status === ItemStatus::DELETED->value) {
            $query = Product::onlyTrashed()->with('category');
        } elseif ($status === ItemStatus::ALL->value) {
            $query = Product::withTrashed()->with('category');
        } else {
            $query = Product::with('category');
        }

        // Search by name
        if (!empty($filter->search)) {
            $query->where('name', 'like', "%{$filter->search}%");
        }

        // Filter by category
        if ($filter->categoryId !== null) {
            $query->where('category_id', $filter->categoryId);
        }

        // Sort (using default sort)
        $sortBy = 'id';
        $sortOrder = 'desc';

        if (in_array($sortBy, ['id', 'name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    /**
     * Create a new product
     */
    public function create(array $data)
    {
        return Product::create($data);
    }

    /**
     * Update a product
     */
    public function update($product, array $data)
    {
        $product->update($data);
        return $product;
    }

    /**
     * Delete a product (soft delete)
     */
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }

    /**
     * Get trashed products
     */
    public function getTrashed($perPage = 10)
    {
        return Product::onlyTrashed()->with('category')->latest('deleted_at')->paginate($perPage);
    }

    /**
     * Restore a product
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if (!$product->trashed()) {
            return [
                'success' => false,
                'message' => 'Product is not deleted.'
            ];
        }

        $product->restore();

        return [
            'success' => true,
            'message' => 'Product restored successfully',
            'data' => $product
        ];
    }

    /**
     * Force delete a product
     */
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();
    }

    /**
     * Paginate products
     */
    public function paginate($perPage = 10)
    {
        return Product::paginate($perPage);
    }
}
