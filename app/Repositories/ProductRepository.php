<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductImage;
use App\DTOs\ProductFilterDTO;
use Illuminate\Support\Facades\DB;

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

    /**
     * Set product primary image by image id.
     */
    public function setPrimaryProductImage(int $productId, int $imageId): void
    {
        DB::transaction(function () use ($productId, $imageId) {
            $image = ProductImage::query()
                ->where('product_id', $productId)
                ->findOrFail($imageId);

            ProductImage::query()
                ->where('product_id', $productId)
                ->update(['is_primary' => false]);

            $image->update(['is_primary' => true]);
        });
    }

    /**
     * Move product image left by swapping sort order.
     */
    public function moveProductImageLeft(int $productId, int $imageId): void
    {
        DB::transaction(function () use ($productId, $imageId) {
            $current = ProductImage::query()
                ->where('product_id', $productId)
                ->findOrFail($imageId);

            $leftNeighbor = ProductImage::query()
                ->where('product_id', $productId)
                ->where(function ($query) use ($current) {
                    $query->where('sort_order', '<', $current->sort_order)
                        ->orWhere(function ($subQuery) use ($current) {
                            $subQuery->where('sort_order', $current->sort_order)
                                ->where('id', '<', $current->id);
                        });
                })
                ->orderByDesc('sort_order')
                ->orderByDesc('id')
                ->first();

            if (!$leftNeighbor) {
                return;
            }

            $currentSort = $current->sort_order;
            $current->update(['sort_order' => $leftNeighbor->sort_order]);
            $leftNeighbor->update(['sort_order' => $currentSort]);
        });
    }

    /**
     * Move product image right by swapping sort order.
     */
    public function moveProductImageRight(int $productId, int $imageId): void
    {
        DB::transaction(function () use ($productId, $imageId) {
            $current = ProductImage::query()
                ->where('product_id', $productId)
                ->findOrFail($imageId);

            $rightNeighbor = ProductImage::query()
                ->where('product_id', $productId)
                ->where(function ($query) use ($current) {
                    $query->where('sort_order', '>', $current->sort_order)
                        ->orWhere(function ($subQuery) use ($current) {
                            $subQuery->where('sort_order', $current->sort_order)
                                ->where('id', '>', $current->id);
                        });
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if (!$rightNeighbor) {
                return;
            }

            $currentSort = $current->sort_order;
            $current->update(['sort_order' => $rightNeighbor->sort_order]);
            $rightNeighbor->update(['sort_order' => $currentSort]);
        });
    }

    /**
     * Delete product image and keep primary image consistent.
     */
    public function deleteProductImage(int $productId, int $imageId): void
    {
        DB::transaction(function () use ($productId, $imageId) {
            $image = ProductImage::query()
                ->where('product_id', $productId)
                ->findOrFail($imageId);

            $wasPrimary = (bool) $image->is_primary;
            $image->delete();

            if (!$wasPrimary) {
                return;
            }

            $nextPrimary = ProductImage::query()
                ->where('product_id', $productId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
            }
        });
    }
}
