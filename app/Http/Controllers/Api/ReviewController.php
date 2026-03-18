<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ReviewServiceInterface;
use App\DTOs\CreateReviewDTO;
use App\DTOs\ReviewFilterDTO;
use App\Http\Requests\ReviewIndexRequest;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends BaseController
{
    public function __construct(
        private ReviewServiceInterface $reviewService
    ) {}

    /**
     * @OA\Get(
        *     path="/api/products/{productId}/reviews",
     *     summary="List product reviews",
        *     description="Get paginated product reviews with optional rating filter and sorting",
     *     tags={"Reviews"},
        *     @OA\Parameter(
        *         name="productId",
        *         in="path",
        *         required=true,
        *         description="ID of the product to retrieve reviews for",
        *         @OA\Schema(type="integer", example=12)
        *     ),
        *     @OA\Parameter(
        *         name="page",
        *         in="query",
        *         required=false,
        *         description="Pagination page number",
        *         @OA\Schema(type="integer", minimum=1, example=1)
        *     ),
        *     @OA\Parameter(
        *         name="per_page",
        *         in="query",
        *         required=false,
        *         description="Number of reviews per page",
        *         @OA\Schema(type="integer", minimum=1, maximum=50, default=10, example=10)
        *     ),
        *     @OA\Parameter(
        *         name="rating",
        *         in="query",
        *         required=false,
        *         description="Minimum rating filter (supports decimal values like 1, 1.5, 2, ..., 5)",
        *         @OA\Schema(type="number", format="float", minimum=1, maximum=5, example=4.5)
        *     ),
        *     @OA\Parameter(
        *         name="sort",
        *         in="query",
        *         required=false,
        *         description="Sort reviews by creation date or rating",
        *         @OA\Schema(type="string", enum={"latest","oldest","highest_rating"}, example="latest")
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Reviews retrieved successfully",
        *         @OA\JsonContent(
        *             type="object",
        *             @OA\Property(property="success", type="boolean", example=true),
        *             @OA\Property(property="message", type="string", example="Reviews retrieved successfully"),
        *             @OA\Property(
        *                 property="data",
        *                 type="array",
        *                 @OA\Items(
        *                     type="object",
        *                     @OA\Property(property="id", type="integer", example=1),
        *                     @OA\Property(property="rating", type="integer", example=5),
        *                     @OA\Property(property="comment", type="string", example="Great product"),
        *                     @OA\Property(property="created_at", type="string", example="2026-03-18"),
        *                     @OA\Property(
        *                         property="user",
        *                         type="object",
        *                         @OA\Property(property="name", type="string", example="John Doe")
        *                     )
        *                 )
        *             ),
        *             @OA\Property(
        *                 property="meta",
        *                 type="object",
        *                 @OA\Property(property="current_page", type="integer", example=1),
        *                 @OA\Property(property="last_page", type="integer", example=3),
        *                 @OA\Property(property="per_page", type="integer", example=10),
        *                 @OA\Property(property="total", type="integer", example=27)
        *             )
        *         )
        *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
        public function index(ReviewIndexRequest $request, int $productId)
    {
        $filter = ReviewFilterDTO::fromRequest($request, (int) $productId);
        $reviews = $this->reviewService->getProductReviews($filter);

        return $this->success(
            ReviewResource::collection($reviews),
            'Reviews retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products/{id}/reviews",
     *     summary="Create product review",
     *     description="Create a product review. Supports guest and authenticated users",
     *     tags={"Reviews"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating","comment"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
     *             @OA\Property(property="comment", type="string", maxLength=1000, example="Great product")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Review created successfully"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreReviewRequest $request, $productId)
    {
        $validated = $request->validated();

        $dto = CreateReviewDTO::fromArray([
            'product_id' => (int) $productId,
            'user_id' => auth('sanctum')->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        $review = $this->reviewService->createReview($dto);

        return $this->success(
            new ReviewResource($review),
            'Review created successfully',
            Response::HTTP_CREATED
        );
    }
}
