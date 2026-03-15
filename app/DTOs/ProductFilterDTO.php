<?php

namespace App\DTOs;

use App\Http\Requests\ProductIndexRequest;

/**
 * Data Transfer Object for product filtering
 *
 * Represents validated filter parameters for listing products.
 * Decouples the incoming request format from the service layer.
 */
class ProductFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $status = null,
        public readonly ?float $priceMin = null,
        public readonly ?float $priceMax = null,
        public readonly ?string $sort = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}

    /**
     * Create a DTO from ProductIndexRequest
     */
    public static function fromRequest(ProductIndexRequest $request): self
    {
        $status = $request->input('status');

        return new self(
            search: $request->input('search'),
            categoryId: $request->input('category_id') ? (int) $request->input('category_id') : null,
            status: $status === 'trashed' ? 'deleted' : $status,
            priceMin: $request->filled('price_min') ? (float) $request->input('price_min') : null,
            priceMax: $request->filled('price_max') ? (float) $request->input('price_max') : null,
            sort: $request->input('sort'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
        );
    }

    /**
     * Convert DTO to array for passing to repository
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'status' => $this->status,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'sort' => $this->sort,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }

    /**
     * Convert full filter state to a stable cache key hash.
     */
    public function toCacheKey(): string
    {
        return md5(json_encode([
            'page' => $this->page,
            'per_page' => $this->perPage,
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'status' => $this->status,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'sort' => $this->sort,
        ]));
    }
}
