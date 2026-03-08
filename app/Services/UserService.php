<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Enums\UserRole;
use App\Enums\ItemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BusinessException;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Build query with search and filters
     */
    public function buildQuery(Request $request)
    {
        return $this->userRepository->buildQuery($request);
    }

    /**
     * Get paginated users data with metadata
     */
    public function getListData(Request $request)
    {
        $perPage = (int)$request->input('per_page', 15);
        $users = $this->userRepository->getAll($request, $perPage);

        return [
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status', ItemStatus::ACTIVE->value),
                'role' => $request->input('role'),
                'sort_by' => $request->input('sort_by', 'id'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ],
            'paginator' => $users
        ];
    }

    /**
     * Get role options
     */
    public function getRoles()
    {
        return UserRole::options();
    }

    /**
     * Create a new user
     */
    public function createUser(CreateUserDTO $dto)
    {
        $data = $dto->toArray();
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    /**
     * Retrieve a single user by id
     */
    public function getUser($id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Update user by id
     *
     * @throws BusinessException when business rules are violated
     */
    public function updateUser($id, UpdateUserDTO $dto)
    {
        $user = $this->getUser($id);
        $data = $dto->toArray();

        // Prevent admin from removing their own admin role
        if (Auth::id() === $user->id && ($data['role'] ?? $user->role) !== UserRole::ADMIN->value) {
            throw new BusinessException('You cannot remove your own admin role.');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepository->update($user, $data);
    }

    /**
     * Delete user (soft delete) by id
     *
     * @throws BusinessException
     */
    public function deleteUser($id)
    {
        $user = $this->getUser($id);

        // Prevent deleting yourself
        if (Auth::id() === $user->id) {
            throw new BusinessException('You cannot delete your own account.');
        }

        return $this->userRepository->delete($id);
    }

    /**
     * Get trashed users
     */
    public function getTrashed(\Illuminate\Http\Request $request)
    {
        return $this->userRepository->getTrashed($request);
    }

    /**
     * Restore user
     */
    public function restoreUser($id)
    {
        return $this->userRepository->restore($id);
    }

    /**
     * Force delete user
     */
    public function forceDeleteUser($id)
    {
        return $this->userRepository->forceDelete($id);
    }
}
