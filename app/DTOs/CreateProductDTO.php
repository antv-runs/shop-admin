<?php

namespace App\DTOs;

/**
 * Data Transfer Object for creating a product
 *
 * Represents validated product data from the controller layer.
 * Decouples the incoming request format from the service layer.
 */
class CreateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly ?float $compare_price = null,
        public readonly ?string $description = null,
        public readonly ?string $details = null,
        public readonly array $colors = [],
        public readonly array $sizes = [],
        public readonly string $currency = 'USD',
        public readonly bool $is_active = true,
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
            details: $data['details'] ?? null,
            colors: $data['colors'] ?? [],
            sizes: $data['sizes'] ?? [],
            currency: strtoupper((string) ($data['currency'] ?? 'USD')),
            is_active: isset($data['is_active']) ? (bool) $data['is_active'] : true,
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
            'details' => $this->details,
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'category_id' => $this->category_id,
        ];
    }
}
