<?php

namespace App\DTOs;

/**
 * Data Transfer Object for creating a category
 *
 * Represents validated category creation data from the controller layer.
 * Decouples the incoming request format from the service layer.
 */
class CreateCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}

    /**
     * Create a DTO from validated form request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
        );
    }

    /**
     * Convert DTO to array for passing to models/repository
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
