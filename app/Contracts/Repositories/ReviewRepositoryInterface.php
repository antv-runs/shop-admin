<?php

namespace App\Contracts\Repositories;

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
}
