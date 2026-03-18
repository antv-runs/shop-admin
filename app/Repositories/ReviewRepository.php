<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

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

    /**
     * Get paginated reviews with admin filters.
     */
    public function paginateWithFilters(array $filters)
    {
        $query = Review::query()->with(['product:id,name', 'user:id,email,name']);

        $search = (string) ($filters['search'] ?? '');
        if ($search !== '') {
            $query->leftJoin('users', 'users.id', '=', 'reviews.user_id')
                ->where('users.email', 'like', '%' . $search . '%')
                ->select('reviews.*');
        }

        if (isset($filters['rating']) && $filters['rating'] !== null) {
            $query->where('reviews.rating', (float) $filters['rating']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('reviews.product_id', (int) $filters['product_id']);
        }

        $sort = (string) ($filters['sort'] ?? 'latest');
        $sortColumn = (string) ($filters['sort_column'] ?? 'reviews.created_at');
        $sortDirection = (string) ($filters['sort_direction'] ?? 'desc');

        if ($sort === 'highest') {
            $query->orderBy($sortColumn, $sortDirection)
                ->orderBy('reviews.created_at', 'desc');
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        return $query->paginate(
            (int) ($filters['per_page'] ?? 10),
            ['*'],
            'page',
            (int) ($filters['page'] ?? 1)
        );
    }

    /**
     * Update a review.
     */
    public function update(Review $review, array $data)
    {
        $review->update($data);

        return $review->load(['product:id,name', 'user:id,email,name']);
    }

    /**
     * Delete a review.
     */
    public function delete(Review $review): void
    {
        $review->delete();
    }

    /**
     * Get products for review forms/filters.
     */
    public function getReviewProducts()
    {
        return Product::query()->select(['id', 'name'])->orderBy('name')->get();
    }

    /**
     * Get users for review forms.
     */
    public function getReviewUsers()
    {
        return User::query()->select(['id', 'name', 'email'])->orderBy('name')->get();
    }

    /**
     * Ensure review has admin relations loaded.
     */
    public function loadReviewRelations(Review $review): Review
    {
        return $review->load(['product:id,name', 'user:id,name,email']);
    }
}
