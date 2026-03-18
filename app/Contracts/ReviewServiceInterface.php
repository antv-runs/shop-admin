<?php

namespace App\Contracts;

use App\DTOs\CreateReviewDTO;
use App\DTOs\ReviewFilterDTO;

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
}
