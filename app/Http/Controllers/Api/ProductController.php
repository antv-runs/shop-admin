<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductApiRequest;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ExportProductRequest;
use App\Http\Resources\ProductResource;
use App\Contracts\FileUploadServiceInterface;
use App\Contracts\ProductServiceInterface;
use App\DTOs\CreateProductDTO;
use App\DTOs\UpdateProductDTO;
use App\DTOs\ProductFilterDTO;
use App\DTOs\UploadImageDTO;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends BaseController
{
    private ProductServiceInterface $productService;
    private FileUploadServiceInterface $fileUploadService;

    public function __construct(ProductServiceInterface $productService, FileUploadServiceInterface $fileUploadService)
    {
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * List products with filters and pagination
     *
     * @OA\Get(
     *     path="/api/products",
     *     summary="List products with filters and pagination",
     *     description="Retrieve paginated products. Public endpoint with optional filtering by search, category, and status.",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name",
     *         required=false,
     *         @OA\Schema(type="string", example="t-shirt")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "deleted", "all"}, example="active")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function index(ProductIndexRequest $request)
    {
        $filter = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getAllProducts($filter);

        return $this->success(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     description="Create a new product (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "price", "currency"},
     *                 @OA\Property(property="name", type="string", maxLength=255),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0),
     *                 @OA\Property(property="compare_price", type="number", format="float", nullable=true),
     *                 @OA\Property(property="description", type="string", nullable=true),
     *                 @OA\Property(property="details", type="string", nullable=true),
     *                 @OA\Property(property="colors", type="array", @OA\Items(type="string"), nullable=true),
     *                 @OA\Property(property="sizes", type="array", @OA\Items(type="string"), nullable=true),
     *                 @OA\Property(property="currency", type="string", default="USD"),
     *                 @OA\Property(property="is_active", type="boolean", default=true),
     *                 @OA\Property(property="category_id", type="integer", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(ProductApiRequest $request)
    {
        $validated = $request->validated();

        // Create DTO from validated data
        $dto = CreateProductDTO::fromArray($validated);

        $product = $this->productService->createProduct($dto);

        return $this->success(
            new ProductResource($product['data']),
            'Product created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products/upload",
     *     summary="Upload product images",
     *     description="Upload product images (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id", "images[]"},
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Images uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function upload(UploadImageRequest $request)
    {
        $dto = UploadImageDTO::fromArray([
            'id' => (int) $request->input('id'),
            'images' => $request->file('images', []),
        ]);

        $product = $this->productService->uploadProductImage($dto);

        return $this->success(
            new ProductResource($product),
            'Product images uploaded successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a single product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        try {
            $product = $this->productService->getProduct($id);

            return $this->success(
                new ProductResource($product),
                'Product retrieved successfully'
            );
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products/slug/{slug}",
     *     summary="Get a single product by slug",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Product slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function showBySlug(string $slug)
    {
        $product = $this->productService->findProductBySlugForStore($slug);

        return $this->success(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     description="Update product details (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product data (all fields optional for PATCH)",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=255),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0),
     *                 @OA\Property(property="compare_price", type="number", format="float", nullable=true),
     *                 @OA\Property(property="description", type="string", nullable=true),
     *                 @OA\Property(property="details", type="string", nullable=true),
     *                 @OA\Property(property="colors", type="array", @OA\Items(type="string"), nullable=true),
     *                 @OA\Property(property="sizes", type="array", @OA\Items(type="string"), nullable=true),
     *                 @OA\Property(property="currency", type="string"),
     *                 @OA\Property(property="is_active", type="boolean"),
     *                 @OA\Property(property="category_id", type="integer", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(ProductApiRequest $request, $id)
    {
        $product = $this->productService->getProduct($id);
        $validated = $request->validated();

        // Create DTO from validated data
        $dto = UpdateProductDTO::fromArray($validated);

        $result = $this->productService->updateProduct($product, $dto);

        return $this->success(
            new ProductResource($result['data'] ?? $product),
            'Product updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Soft delete a product",
     *     description="Soft delete a product (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy($id)
    {
        $this->productService->deleteProduct($id);

        return $this->success(
            null,
            'Product deleted successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/products/trashed",
     *     summary="List soft-deleted products",
     *     description="List soft-deleted products with pagination (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trashed products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function trashed(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);

        $products = $this->productService->getTrashed($perPage);

        return $this->success(
            ProductResource::collection($products),
            'Trashed products retrieved successfully'
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}/restore",
     *     summary="Restore a soft-deleted product",
     *     description="Restore a soft-deleted product (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function restore($id)
    {
        $product = $this->productService->restoreProduct($id);
        return $this->success(
            new ProductResource($product),
            'Product restored successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}/force-delete",
     *     summary="Permanently delete a product",
     *     description="Permanently delete a product (Admin only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product permanently deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function forceDelete($id)
    {
        $this->productService->forceDeleteProduct($id);
        return $this->success(
            null,
            'Product permanently deleted'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products/export",
     *     summary="Export products to file",
     *     description="Export products to CSV or Excel format (Admin only, async)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Export parameters",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"format"},
     *                 @OA\Property(property="format", type="string", enum={"xlsx", "csv"}, example="xlsx"),
     *                 @OA\Property(property="search", type="string", nullable=true),
     *                 @OA\Property(property="category_id", type="integer", nullable=true),
     *                 @OA\Property(property="status", type="string", enum={"active", "deleted", "all"}, nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Export job queued",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function export(ExportProductRequest $request)
    {
        $data = $request->validated();

        // Get current authenticated user
        $user = auth()->user();

        // Dispatch export job
        $this->productService->exportProducts(
            $user->id,
            [
                'search' => $data['search'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'status' => $data['status'] ?? 'active',
            ],
            $data['format']
        );

        return $this->success(
            ['format' => $request->input('format')],
            'Export job queued. You will receive an email with the download link shortly.',
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Get(
     *     path="/api/products/exports/{filename}",
     *     summary="Download a previously exported file",
     *     description="Download a previously exported product file",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         required=true,
     *         description="Export filename",
     *         @OA\Schema(type="string", example="products_export_2026-03-03_09-41-08.xlsx")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File downloaded successfully",
     *         @OA\MediaType(mediaType="application/octet-stream")
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function downloadExport(Request $request, $filename)
    {
        // Validate filename - allow export files with alphanumeric, underscore, hyphen and dot
        // Pattern: products_export_YYYY-MM-DD_HH-mm-ss.xlsx or .csv
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.(xlsx|csv)$/', $filename)) {
            return $this->error('Invalid filename format', 400);
        }

        // Prevent directory traversal attempts
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
            return $this->error('Invalid filename', 400);
        }

        $filePath = 'exports/' . $filename;

        // Get storage disk - use the same disk where export job stores files
        $disk = config('filesystems.default', 'local');

        // Log for debugging
        Log::debug("Attempting to download export", [
            'filename' => $filename,
            'filePath' => $filePath,
            'disk' => $disk,
            'authenticated' => auth()->check()
        ]);

        // Check if file exists
        if (!$this->fileUploadService->fileExists($filePath)) {
            Log::warning("Export file not found", [
                'filename' => $filename,
                'filePath' => $filePath,
                'disk' => $disk
            ]);
            return $this->error('File not found', Response::HTTP_NOT_FOUND);
        }

        // Get file last modified time for caching
        $lastModified = $this->fileUploadService->getLastModified($filePath);

        // Determine content type based on extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $contentType = $extension === 'xlsx'
            ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            : 'text/csv';

        // return a download stream with proper headers
        return $this->fileUploadService->download(
            $filePath,
            $filename,
            [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=3600',
                'Last-Modified' => gmdate('D, d M Y H:i:s T', $lastModified)
            ]
        );
    }

}
