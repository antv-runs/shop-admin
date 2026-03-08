<?php

namespace App\DTOs;

/**
 * Data Transfer Object for updating a user
 *
 * Represents validated user update data from the controller layer.
 * Decouples the incoming request format from the service layer.
 */
class UpdateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly string $role = 'user',
    ) {}

    /**
     * Create a DTO from validated form request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            role: $data['role'] ?? 'user',
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
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}
