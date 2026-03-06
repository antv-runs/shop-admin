<?php

namespace App\Services;

use App\Contracts\ProductServiceInterface;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ItemStatus;
use App\Jobs\ExportProductsJob;
use Illuminate\Support\Facades\Storage;

class ProductService implements ProductServiceInterface
{
    /**
     * Get all products with category
     */
    public function getAllProducts(\Illuminate\Http\Request $request, $perPage = 10)
    {
        $perPage = (int)$request->input('per_page', $perPage);

        // Query builder base on status
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
     * Get all categories
     */
    public function getCategories()
    {
        return Category::all();
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    /**
     * Get product by ID
     */
    public function getProduct($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, array $data)
    {
        // If a new image is provided, remove old image first
        if (!empty($data['image']) && $product->image) {
            try {
                Storage::disk('public')->delete($product->image);
            } catch (\Throwable $e) {
                // ignore deletion errors
            }
        }

        $product->update($data);
        return $product;
    }

    /**
     * Delete product (soft delete)
     */
    public function deleteProduct($id)
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
     * Restore product
     */
    public function restoreProduct($id)
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
     * Force delete product
     */
    public function forceDeleteProduct($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        // delete image file if exists
        if ($product->image) {
            try {
                Storage::disk('public')->delete($product->image);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $product->forceDelete();
    }

    /**
     * Export products to CSV/Excel via queue
     * Dispatches a job to the queue for async processing
     *
     * @param int $userId User who requested the export
     * @param array $filters Filter parameters (search, category_id, status)
     * @param string $format Export format: 'csv' or 'excel'
     * @return void
     */
    public function exportProducts(int $userId, array $filters = [], string $format = 'csv'): void
    {
        ExportProductsJob::dispatch($userId, $filters, $format);
    }
}
