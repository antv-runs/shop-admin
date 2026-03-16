<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductImage;
use App\DTOs\ProductFilterDTO;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Find a product by ID
     */
    public function findById($id)
    {
        return Product::query()
            ->with(['images', 'primaryImage', 'category'])
            ->findOrFail($id);
    }

    /**
     * Get all products with optional filters
     */
    public function getAll(ProductFilterDTO $filter)
    {
        $query = Product::query()
            ->with(['category', 'images', 'primaryImage'])
            ->where('is_active', true)
            ->latest('id');

        if (!empty($filter->search)) {
            $query->where('name', 'like', "%{$filter->search}%");
        }

        if ($filter->categoryId !== null) {
            $query->where('category_id', $filter->categoryId);
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

    /**
     * Find public product by slug
     */
    public function findPublicBySlug(string $slug)
    {
        return Product::query()
            ->with(['category', 'images', 'primaryImage'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Get related public product IDs
     */
    public function getRelatedPublicProductIds(int $productId, ?int $categoryId = null, int $limit = 4): array
    {
        $query = Product::query()
            ->where('id', '!=', $productId)
            ->latest('id');

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        return $query->limit($limit)->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    /**
     * Create gallery image record for product
     */
    public function createProductImage($productId, $path, bool $isPrimary = false)
    {
        $nextSortOrder = (int) ProductImage::query()
            ->where('product_id', $productId)
            ->max('sort_order') + 1;

        return ProductImage::create([
            'product_id' => $productId,
            'image_url' => $path,
            'sort_order' => $nextSortOrder,
            'is_primary' => $isPrimary,
        ]);
    }
}
