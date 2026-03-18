<?php

namespace App\Contracts;

use App\DTOs\CreateReviewDTO;
use App\DTOs\ReviewAdminFilterDTO;
use App\DTOs\ReviewFilterDTO;
use App\DTOs\StoreReviewDTO;
use App\DTOs\UpdateReviewDTO;
use App\Models\Review;

interface ReviewServiceInterface
{
    /**
     * Get paginated reviews for a product.
     */
    public function getProductReviews(ReviewFilterDTO $filter);

    /**
     * Create a new product review.
     */
    public function createReview(CreateReviewDTO $dto);

    /**
     * Get paginated reviews for admin management page.
     */
    public function paginateWithFilters(ReviewAdminFilterDTO $filter);

    /**
     * Create a review from admin DTO.
     */
    public function create(StoreReviewDTO $dto);

    /**
     * Update a review from admin DTO.
     */
    public function update(Review $review, UpdateReviewDTO $dto);

    /**
     * Delete a review.
     */
    public function delete(Review $review): void;

    /**
     * Get products for admin review forms and filters.
     */
    public function getReviewProducts();

    /**
     * Get users for admin review forms.
     */
    public function getReviewUsers();

    /**
     * Ensure review has relations needed for admin edit view.
     */
    public function loadReviewRelations(Review $review): Review;
}
