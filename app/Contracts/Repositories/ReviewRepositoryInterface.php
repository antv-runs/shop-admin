<?php

namespace App\Contracts\Repositories;

use App\Models\Review;

interface ReviewRepositoryInterface
{
    /**
     * Get paginated reviews by product.
     */
    public function getByProduct(
        int $productId,
        int $page,
        int $perPage,
        ?float $minRating,
        string $sortColumn,
        string $sortDirection
    );

    /**
     * Create a review.
     */
    public function create(array $data);

    /**
     * Get paginated reviews with admin filters.
     */
    public function paginateWithFilters(array $filters);

    /**
     * Update a review.
     */
    public function update(Review $review, array $data);

    /**
     * Delete a review.
     */
    public function delete(Review $review): void;

    /**
     * Get products for review forms/filters.
     */
    public function getReviewProducts();

    /**
     * Get users for review forms.
     */
    public function getReviewUsers();

    /**
     * Ensure review has admin relations loaded.
     */
    public function loadReviewRelations(Review $review): Review;
}
