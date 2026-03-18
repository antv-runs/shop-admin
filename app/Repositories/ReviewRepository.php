<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\Product;
use App\Models\Review;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * Get paginated reviews for a product with optional filters.
     */
    public function getByProduct(
        int $productId,
        int $page,
        int $perPage,
        ?float $minRating,
        string $sortColumn,
        string $sortDirection
    )
    {
        Product::query()->findOrFail($productId);

        $query = Review::query()
            ->with('user')
            ->where('product_id', $productId);

        if (!is_null($minRating)) {
            $query->where('rating', '>=', $minRating);
        }

        return $query
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a review.
     */
    public function create(array $data)
    {
        return Review::query()
            ->create($data)
            ->load('user');
    }
}
