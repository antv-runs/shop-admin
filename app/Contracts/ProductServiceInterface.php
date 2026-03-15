<?php

namespace App\Contracts;

use App\Models\Product;
use App\DTOs\CreateProductDTO;
use App\DTOs\UpdateProductDTO;
use App\DTOs\UploadImageDTO;
use App\DTOs\ProductFilterDTO;

interface ProductServiceInterface
{
    /**
     * Get all products with category
     */
    public function getAllProducts(ProductFilterDTO $filter);

    /**
     * Get all categories
     */
    public function getCategories();

    /**
     * Create a new product
     */
    public function createProduct(CreateProductDTO $dto);

    /**
     * Get product by ID
     */
    public function getProduct($id);

    /**
     * Update product
     */
    public function updateProduct(Product $product, UpdateProductDTO $dto);

    /**
     * Delete product (soft delete)
     */
    public function deleteProduct($id);

    /**
     * Get trashed products
     */
    public function getTrashed($perPage = 10);

    /**
     * Restore product
     */
    public function restoreProduct($id);

    /**
     * Force delete product
     */
    public function forceDeleteProduct($id);

    /**
     * Export products to CSV/Excel via queue
     */
    public function exportProducts(int $userId, array $filters = [], string $format = 'csv');

    /**
     * Upload a product image and return public URL (S3)
     */
    public function uploadProductImage(UploadImageDTO $dto);

    /**
     * Find storefront product by slug
     */
    public function findProductBySlugForStore(string $slug);

    /**
     * Get related product IDs for storefront detail page
     */
    public function getRelatedProductIdsForStore(int $productId, ?int $categoryId = null, int $limit = 4): array;
}
