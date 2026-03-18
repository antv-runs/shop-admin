<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ReviewFilterDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        public readonly ?float $rating = null,
        public readonly string $sort = 'latest',
    ) {}

    /**
     * Build DTO from query request and route product id.
     */
    public static function fromRequest(Request $request, int $productId): self
    {
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min(50, $perPage));
        $rating = $request->filled('rating') ? (float) $request->input('rating') : null;
        $sort = (string) $request->input('sort', 'latest');

        return new self(
            productId: $productId,
            page: $page,
            perPage: $perPage,
            rating: $rating,
            sort: $sort,
        );
    }
}
