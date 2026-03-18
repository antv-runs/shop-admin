<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ReviewAdminFilterDTO
{
    public function __construct(
        public readonly string $search,
        public readonly string $rating,
        public readonly ?int $productId,
        public readonly string $sort,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $productId = null;
        $rawProductId = (string) $request->query('product_id', '');

        if ($rawProductId !== '' && ctype_digit($rawProductId)) {
            $productId = (int) $rawProductId;
        }

        return new self(
            search: trim((string) $request->query('search', '')),
            rating: (string) $request->query('rating', ''),
            productId: $productId,
            sort: (string) $request->query('sort', 'latest'),
            page: max(1, (int) $request->query('page', 1)),
            perPage: 10,
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'rating' => $this->rating,
            'product_id' => $this->productId,
            'sort' => $this->sort,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
