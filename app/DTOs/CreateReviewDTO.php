<?php

namespace App\DTOs;

class CreateReviewDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly ?int $userId,
        public readonly int $rating,
        public readonly string $comment,
    ) {}

    /**
     * Create a DTO from validated request data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            rating: (int) $data['rating'],
            comment: (string) $data['comment'],
        );
    }

    /**
     * Convert DTO to array for repository.
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ];
    }
}
