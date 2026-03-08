<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Contracts\AuthServiceInterface;
use App\Http\Resources\UserResource;
use App\Exceptions\BusinessException;

class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        Log::info('AuthService: register called', ['email' => $data['email'] ?? null]);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        $user = $this->userRepository->create($userData);
        $token = $user->createToken('api-token')->plainTextToken;

        Log::info('AuthService: register successful', ['user_id' => $user->id]);

        return [
            'user' => $user->only(['id', 'name', 'email']),
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        Log::info('AuthService: login attempt', ['email' => $credentials['email'] ?? null]);

        if (!Auth::attempt($credentials)) {
            Log::warning('AuthService: login failed', ['email' => $credentials['email'] ?? null]);
            throw new BusinessException('Invalid credentials');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        Log::info('AuthService: login successful', ['user_id' => $user->id]);

        return [
            'user' => new UserResource($user),
            'token' => $token,
        ];
    }

    public function logout(Request $request): void
    {
        $user = $request->user();
        if (! $user) {
            Log::warning('AuthService: logout called with no authenticated user');
            throw new BusinessException('Not authenticated');
        }

        $request->user()->currentAccessToken()->delete();
        Log::info('AuthService: logout successful', ['user_id' => $user->id]);
    }

    public function me(Request $request)
    {
        Log::info('AuthService: me called', ['user_id' => optional($request->user())->id]);
        return $request->user();
    }
}
