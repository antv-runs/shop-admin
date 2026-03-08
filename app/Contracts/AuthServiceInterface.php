<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use App\DTOs\RegisterUserDTO;

interface AuthServiceInterface
{
    public function register(RegisterUserDTO $dto): array;

    public function login(array $credentials): array;

    public function logout(Request $request): void;

    public function me(Request $request);
}
