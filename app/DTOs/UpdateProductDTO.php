<?php

namespace App\DTOs;

/**
 * Data Transfer Object for updating a product
 *
 * Represents validated product update data from the controller layer.
 * Decouples the incoming request format from the service layer.
 */
class UpdateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly ?float $compare_price = null,
        public readonly ?string $description = null,
        public readonly ?int $category_id = null,
    ) {}

    /**
     * Create a DTO from validated form request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            compare_price: isset($data['compare_price']) ? (float) $data['compare_price'] : null,
            description: $data['description'] ?? null,
            category_id: $data['category_id'] ?? null,
        );
    }

    /**
     * Convert DTO to array for passing to models/repository
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'description' => $this->description,
            'category_id' => $this->category_id,
        ];
    }
}
