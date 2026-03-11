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
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
