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
        public readonly ?int $parentId = null,
        public readonly ?bool $hasChildren = null,
        public readonly ?string $sort = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}

    /**
     * Create a DTO from CategoryIndexRequest
     */
    public static function fromRequest(CategoryIndexRequest $request): self
    {
        $status = $request->input('status');
        $hasChildren = $request->input('has_children');

        return new self(
            search: $request->input('search'),
            status: $status === 'trashed' ? 'deleted' : $status,
            parentId: $request->filled('parent_id') ? (int) $request->input('parent_id') : null,
            hasChildren: $hasChildren !== null ? filter_var($hasChildren, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null,
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
            'status' => $this->status,
            'parent_id' => $this->parentId,
            'has_children' => $this->hasChildren,
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
            'status' => $this->status,
            'parent_id' => $this->parentId,
            'has_children' => $this->hasChildren,
            'sort' => $this->sort,
        ]));
    }
}
