<?php

namespace App\DTOs;

use App\Http\Requests\Admin\UpdateReviewRequest;

class UpdateReviewDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly ?int $userId,
        public readonly float $rating,
        public readonly string $comment,
        public readonly bool $isVerified,
    ) {}

    public static function fromRequest(UpdateReviewRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            productId: (int) $validated['product_id'],
            userId: isset($validated['user_id']) ? (int) $validated['user_id'] : null,
            rating: (float) $validated['rating'],
            comment: (string) $validated['comment'],
            isVerified: $request->boolean('is_verified'),
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_verified' => $this->isVerified,
        ];
    }
}
