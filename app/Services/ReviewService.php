<?php

namespace App\Services;

use App\Contracts\ReviewServiceInterface;
use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\DTOs\CreateReviewDTO;
use App\DTOs\ReviewFilterDTO;

class ReviewService implements ReviewServiceInterface
{
    public function __construct(
        private ReviewRepositoryInterface $reviewRepository
    ) {}

    /**
     * Get paginated reviews for a product.
     */
    public function getProductReviews(ReviewFilterDTO $filter)
    {
        $sortMapping = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'highest_rating' => ['rating', 'desc'],
        ];

        [$sortColumn, $sortDirection] = $sortMapping[$filter->sort] ?? $sortMapping['latest'];

        return $this->reviewRepository->getByProduct(
            productId: $filter->productId,
            page: $filter->page,
            perPage: $filter->perPage,
            minRating: $filter->rating,
            sortColumn: $sortColumn,
            sortDirection: $sortDirection,
        );
    }

    /**
     * Create a new product review.
     */
    public function createReview(CreateReviewDTO $dto)
    {
        return $this->reviewRepository->create($dto->toArray());
    }
}
