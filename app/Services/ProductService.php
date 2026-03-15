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
use App\DTOs\ProductFilterDTO;
use App\Helpers\CacheHelper;
use App\Constants\CacheKey;
use App\Constants\CacheConstants;
use App\DTOs\UploadImageDTO;
use Illuminate\Support\Facades\DB;

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
     * Cached with TTL of 300 seconds
     */
    public function getAllProducts(ProductFilterDTO $filter)
    {
        $cacheKey = CacheKey::productList($filter->toCacheKey());

        return CacheHelper::rememberWithTags(
            [CacheConstants::TAG_PRODUCT_LIST],
            $cacheKey,
            CacheConstants::CACHE_TTL,
            function () use ($filter) {
                return $this->productRepository->getAll($filter);
            }
        );
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
        $result = $this->productRepository->create($dto->toArray());

        // Invalidate list cache
        $this->invalidateProductListCache();

        return $result;
    }

    /**
     * Get product by ID
     * Cached with TTL of 300 seconds
     */
    public function getProduct($id)
    {
        $cacheKey = CacheKey::productDetail($id);

        return CacheHelper::remember($cacheKey, CacheConstants::CACHE_TTL, function () use ($id) {
            return $this->productRepository->findById($id);
        });
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, UpdateProductDTO $dto)
    {
        $data = $dto->toArray();

        $result = $this->productRepository->update($product, $data);

        // Invalidate caches
        CacheHelper::forget(CacheKey::productDetail($product->id));
        $this->invalidateProductListCache();

        return $result;
    }

    /**
     * Delete product (soft delete)
     */
    public function deleteProduct($id)
    {
        $result = $this->productRepository->delete($id);

        // Invalidate caches
        CacheHelper::forget(CacheKey::productDetail($id));
        $this->invalidateProductListCache();

        return $result;
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
        $result = $this->productRepository->restore($id);

        // Invalidate list cache
        $this->invalidateProductListCache();

        return $result;
    }

    /**
     * Force delete product
     */
    public function forceDeleteProduct($id)
    {
        $this->productRepository->forceDelete($id);

        // Invalidate caches
        CacheHelper::forget(CacheKey::productDetail($id));
        $this->invalidateProductListCache();
    }

    /**
     * Invalidate all product list caches
     */
    private function invalidateProductListCache()
    {
        CacheHelper::flushTags([CacheConstants::TAG_PRODUCT_LIST]);
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

    /**
     * Upload one or more product images and return product with loaded gallery.
     */
    public function uploadProductImage(UploadImageDTO $dto): Product
    {
        $product = $this->productRepository->findById($dto->id);

        DB::transaction(function () use ($dto, $product) {
            foreach ($dto->images as $image) {
                $path = $this->fileUploadService->uploadProductImage($image);
                $this->productRepository->createProductImage($product->id, $path);
            }
        });

        // Invalidate caches
        CacheHelper::forget(CacheKey::productDetail($dto->id));
        $this->invalidateProductListCache();

        return $product->fresh()->load('images');
    }

    /**
     * Find a storefront product by slug
     */
    public function findProductBySlugForStore(string $slug)
    {
        return $this->productRepository->findPublicBySlug($slug);
    }

    /**
     * Get related product IDs for storefront product detail
     */
    public function getRelatedProductIdsForStore(int $productId, ?int $categoryId = null, int $limit = 4): array
    {
        return $this->productRepository->getRelatedPublicProductIds($productId, $categoryId, $limit);
    }
}
