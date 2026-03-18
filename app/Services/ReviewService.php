<?php

namespace App\Services;

use App\Contracts\ReviewServiceInterface;
use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\DTOs\CreateReviewDTO;
use App\DTOs\ReviewAdminFilterDTO;
use App\DTOs\ReviewFilterDTO;
use App\DTOs\StoreReviewDTO;
use App\DTOs\UpdateReviewDTO;
use App\Models\Review;

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

    /**
     * Get paginated reviews for admin management page.
     */
    public function paginateWithFilters(ReviewAdminFilterDTO $filter)
    {
        $sortMapping = [
            'latest' => ['reviews.created_at', 'desc'],
            'oldest' => ['reviews.created_at', 'asc'],
            'highest' => ['reviews.rating', 'desc'],
        ];

        $allowedRatings = ['1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'];

        $rating = in_array($filter->rating, $allowedRatings, true)
            ? (float) $filter->rating
            : null;

        [$sortColumn, $sortDirection] = $sortMapping[$filter->sort] ?? $sortMapping['latest'];

        return $this->reviewRepository->paginateWithFilters([
            'search' => $filter->search,
            'rating' => $rating,
            'product_id' => $filter->productId,
            'sort_column' => $sortColumn,
            'sort_direction' => $sortDirection,
            'sort' => $filter->sort,
            'page' => $filter->page,
            'per_page' => $filter->perPage,
        ]);
    }

    /**
     * Create a review from admin DTO.
     */
    public function create(StoreReviewDTO $dto)
    {
        return $this->reviewRepository->create($dto->toArray());
    }

    /**
     * Update a review from admin DTO.
     */
    public function update(Review $review, UpdateReviewDTO $dto)
    {
        return $this->reviewRepository->update($review, $dto->toArray());
    }

    /**
     * Delete a review.
     */
    public function delete(Review $review): void
    {
        $this->reviewRepository->delete($review);
    }

    /**
     * Get products for admin review forms and filters.
     */
    public function getReviewProducts()
    {
        return $this->reviewRepository->getReviewProducts();
    }

    /**
     * Get users for admin review forms.
     */
    public function getReviewUsers()
    {
        return $this->reviewRepository->getReviewUsers();
    }

    /**
     * Ensure review has relations needed for admin edit view.
     */
    public function loadReviewRelations(Review $review): Review
    {
        return $this->reviewRepository->loadReviewRelations($review);
    }
}
