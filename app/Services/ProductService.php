<?php

namespace App\Services;

use App\Contracts\ProductServiceInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Product;
use App\Jobs\ExportProductsJob;
use App\Contracts\FileUploadServiceInterface;
use App\DTOs\CreateProductDTO;
use App\DTOs\UpdateProductDTO;

class ProductService implements ProductServiceInterface
{
    private FileUploadServiceInterface $fileUploadService;
    private ProductRepositoryInterface $productRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        FileUploadServiceInterface $fileUploadService,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->fileUploadService = $fileUploadService;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all products with category
     */
    public function getAllProducts(\Illuminate\Http\Request $request, $perPage = 10)
    {
        return $this->productRepository->getAll($request, $perPage);
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        return $this->categoryRepository->getAllActive();
    }

    /**
     * Create a new product
     */
    public function createProduct(CreateProductDTO $dto)
    {
        return $this->productRepository->create($dto->toArray());
    }

    /**
     * Get product by ID
     */
    public function getProduct($id)
    {
        return $this->productRepository->findById($id);
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, UpdateProductDTO $dto)
    {
        $data = $dto->toArray();

        // If a new image is provided, remove old image first
        if (!empty($data['image']) && $product->image) {
            // remove old image using upload service (which respects configured disk)
            $this->fileUploadService->deleteFile($product->image);
        }

        return $this->productRepository->update($product, $data);
    }

    /**
     * Delete product (soft delete)
     */
    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }

    /**
     * Get trashed products
     */
    public function getTrashed($perPage = 10)
    {
        return $this->productRepository->getTrashed($perPage);
    }

    /**
     * Restore product
     */
    public function restoreProduct($id)
    {
        return $this->productRepository->restore($id);
    }

    /**
     * Force delete product
     */
    public function forceDeleteProduct($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        // delete image file if exists
        if ($product->image) {
            // reuse service to delete file when forcing delete
            $this->fileUploadService->deleteFile($product->image);
        }

        $this->productRepository->forceDelete($id);
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
