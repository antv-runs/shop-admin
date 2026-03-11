<?php

namespace App\DTOs;

use App\Http\Requests\CategoryIndexRequest;

/**
 * Data Transfer Object for category filtering
 *
 * Represents validated filter parameters for listing categories.
 * Decouples the incoming request format from the service layer.
 */
class CategoryFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}

    /**
     * Create a DTO from CategoryIndexRequest
     */
    public static function fromRequest(CategoryIndexRequest $request): self
    {
        $status = $request->input('status');

        return new self(
            search: $request->input('search'),
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
            'status' => $this->status,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
