<?php

namespace App\DTOs;

/**
 * Data Transfer Object for updating user profile
 *
 * Represents validated profile update data from the controller layer.
 * Decouples the incoming request format from the service layer.
 */
class UpdateUserProfileDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $bio = null,
        public readonly ?string $profile_image = null,
    ) {}

    /**
     * Create a DTO from validated form request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            bio: $data['bio'] ?? null,
            profile_image: $data['profile_image'] ?? null,
        );
    }

    /**
     * Convert DTO to array for passing to models/repository
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'profile_image' => $this->profile_image,
        ];
    }
}
