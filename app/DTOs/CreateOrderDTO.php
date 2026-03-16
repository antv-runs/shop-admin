<?php

namespace App\DTOs;

/**
 * Data Transfer Object for creating an order
 *
 * Represents validated order data from the controller layer.
 * Decouples the incoming request format from the service layer.
 */
class CreateOrderDTO
{
    /**
     * @param int|null $userId
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param string $address
     * @param array<array{product_id: int, quantity: int}> $items
     */
    public function __construct(
        public readonly ?int $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $address,
        public readonly array $items,
    ) {}

    /**
     * Create a DTO from validated form request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            address: $data['address'],
            items: $data['items'],
        );
    }

    /**
     * Convert DTO to array for passing to models/repository
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'items' => $this->items,
        ];
    }
}
