<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CategoryApiRequest;
use App\Http\Requests\CategoryIndexRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\DTOs\CreateCategoryDTO;
use App\DTOs\CategoryFilterDTO;
use App\DTOs\UpdateCategoryDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends BaseController
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="List storefront categories",
     *     description="Returns a paginated collection of active categories. Supports filtering by search, status, parent_id, has_children, sort.",
     *     tags={"Categories"},
     *     @OA\Parameter(name="search", in="query", required=false, description="max:255", @OA\Schema(type="string", maxLength=255)),
     *     @OA\Parameter(name="status", in="query", required=false, description="enum: active, deleted, all, trashed", @OA\Schema(type="string", enum={"active", "deleted", "all", "trashed"})),
     *     @OA\Parameter(name="parent_id", in="query", required=false, description="must exist in categories table", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="has_children", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort", in="query", required=false, description="enum: name, created_at", @OA\Schema(type="string", enum={"name", "created_at"})),
     *     @OA\Parameter(name="page", in="query", required=false, description="min:1", @OA\Schema(type="integer", minimum=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, description="min:1, max:100", @OA\Schema(type="integer", minimum=1, maximum=100)),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *                 @OA\Property(property="hasChildren", type="boolean")
     *             )),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="prev", type="string", nullable=true),
     *                 @OA\Property(property="next", type="string", nullable=true)
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             ),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully")
     *         )
     *     )
     * )
     */
    public function index(CategoryIndexRequest $request)
    {
        $filter = CategoryFilterDTO::fromRequest($request);
        $categories = $this->categoryService->getCategoriesForStore($filter);

        return $this->success(
            CategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     description="Admin only. Creates a category with a unique name.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", minLength=3, maxLength=100, description="unique in categories table"),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *                 @OA\Property(property="hasChildren", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="unauthenticated"),
     *     @OA\Response(response=403, description="forbidden (non-admin)"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(CategoryApiRequest $request)
    {
        $validated = $request->validated();

        // Create DTO from validated data
        $dto = CreateCategoryDTO::fromArray($validated);

        $category = $this->categoryService->createCategory($dto);

        return $this->success(
            new CategoryResource($category),
            'Category created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get a single category by ID",
     *     description="Returns a single category. No authentication required.",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *                 @OA\Property(property="hasChildren", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found."),
     *             @OA\Property(property="errors", type="string", nullable=true, example=null)
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $category = $this->categoryService->getCategory($id);
        return $this->success(
            new CategoryResource($category),
            'Category retrieved successfully'
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     description="Admin only. Updates name and/or description. Name must be unique, ignoring the current category's own record.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", minLength=3, maxLength=100, description="unique ignoring current id"),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *                 @OA\Property(property="hasChildren", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="unauthenticated"),
     *     @OA\Response(response=403, description="forbidden"),
     *     @OA\Response(response=404, description="not found"),
     *     @OA\Response(
     *         response=422,
     *         description="validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function update(CategoryApiRequest $request, $id)
    {
        $category = $this->categoryService->getCategory($id);
        $validated = $request->validated();

        // Create DTO from validated data
        $dto = UpdateCategoryDTO::fromArray($validated);

        $result = $this->categoryService->updateCategory($category, $dto);

        return $this->success(
            new CategoryResource($result),
            'Category updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Soft delete a category",
     *     description="Admin only. Soft deletes a category (recoverable via restore). Returns no data payload.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully"),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(response=401, description="unauthenticated"),
     *     @OA\Response(response=403, description="forbidden"),
     *     @OA\Response(response=404, description="not found")
     * )
     */
    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        return $this->success(
            null,
            'Category deleted successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/categories/trashed",
     *     summary="List soft-deleted categories",
     *     description="Admin only. Returns paginated soft-deleted categories. Hardcoded page size of 15 items.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trashed categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *                 @OA\Property(property="hasChildren", type="boolean")
     *             )),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="prev", type="string", nullable=true),
     *                 @OA\Property(property="next", type="string", nullable=true)
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer")
     *             ),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trashed categories retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="unauthenticated"),
     *     @OA\Response(response=403, description="forbidden")
     * )
     */
    public function trashed(Request $request)
    {
        $categories = $this->categoryService->getTrashed(15);
        return $this->success(
            CategoryResource::collection($categories),
            'Trashed categories retrieved successfully'
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/categories/{id}/restore",
     *     summary="Restore a soft-deleted category",
     *     description="Admin only. Recovers a previously soft-deleted category.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Category restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category restored successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="href", type="string"),
     *                 @OA\Property(property="hasChildren", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="unauthenticated"),
     *     @OA\Response(response=403, description="forbidden"),
     *     @OA\Response(response=404, description="not found")
     * )
     */
    public function restore($id)
    {
        $category = $this->categoryService->restoreCategory($id);
        return $this->success(
            new CategoryResource($category),
            'Category restored successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}/force-delete",
     *     summary="Permanently delete a category",
     *     description="Admin only. Permanently removes a category. Cannot be undone. Returns no data payload.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Category permanently deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category permanently deleted"),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(response=401, description="unauthenticated"),
     *     @OA\Response(response=403, description="forbidden"),
     *     @OA\Response(response=404, description="not found")
     * )
     */
    public function forceDelete($id)
    {
        $this->categoryService->forceDeleteCategory($id);

        return $this->success(
            null,
            'Category permanently deleted'
        );
    }

}
