<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Enums\ItemStatus;

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
    public function getAll($request, $perPage = 10)
    {
        $perPage = (int)$request->input('per_page', $perPage);

        // Query builder based on status
        $status = $request->input('status', ItemStatus::ACTIVE->value);

        if ($status === ItemStatus::DELETED->value) {
            $query = Product::onlyTrashed()->with('category');
        } elseif ($status === ItemStatus::ALL->value) {
            $query = Product::withTrashed()->with('category');
        } else {
            $query = Product::with('category');
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
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
