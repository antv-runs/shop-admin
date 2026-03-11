<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\DTOs\UserFilterDTO;
use App\Enums\UserRole;
use App\Enums\ItemStatus;
use App\Helpers\CacheHelper;
use App\Constants\CacheKey;
use App\Constants\CacheConstants;
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
     * Get paginated users data with metadata
     * Cached with TTL of 300 seconds
     */
    public function getListData(UserFilterDTO $filter)
    {
        $status = $filter->status ?? ItemStatus::ACTIVE->value;

        $cacheKey = CacheKey::userList(
            $filter->page,
            $filter->perPage,
            $filter->search ?? '',
            $status,
            '',
            'id',
            'desc'
        );

        return CacheHelper::rememberWithTags([CacheConstants::TAG_USER_LIST], $cacheKey, CacheConstants::CACHE_TTL, function () use ($filter, $status) {
            $users = $this->userRepository->getAll($filter);

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
                    'search' => $filter->search,
                    'status' => $status,
                    'per_page' => $filter->perPage,
                ],
                'paginator' => $users
            ];
        });
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

        $result = $this->userRepository->create($data);

        // Invalidate list cache
        $this->invalidateUserListCache();

        return $result;
    }

    /**
     * Retrieve a single user by id
     * Cached with TTL of 300 seconds
     */
    public function getUser($id)
    {
        $cacheKey = CacheKey::userDetail($id);

        return CacheHelper::remember($cacheKey, CacheConstants::CACHE_TTL, function () use ($id) {
            return $this->userRepository->findById($id);
        });
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

        $result = $this->userRepository->update($user, $data);

        // Invalidate caches
        CacheHelper::forget(CacheKey::userDetail($id));
        $this->invalidateUserListCache();

        return $result;
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

        $result = $this->userRepository->delete($id);

        // Invalidate caches
        CacheHelper::forget(CacheKey::userDetail($id));
        $this->invalidateUserListCache();

        return $result;
    }

    /**
     * Get trashed users
     */
    public function getTrashed(UserFilterDTO $filter)
    {
        return $this->userRepository->getTrashed($filter);
    }

    /**
     * Restore user
     */
    public function restoreUser($id)
    {
        $result = $this->userRepository->restore($id);

        // Invalidate list cache
        $this->invalidateUserListCache();

        return $result;
    }

    /**
     * Force delete user
     */
    public function forceDeleteUser($id)
    {
        $result = $this->userRepository->forceDelete($id);

        // Invalidate caches
        CacheHelper::forget(CacheKey::userDetail($id));
        $this->invalidateUserListCache();

        return $result;
    }

    /**
     * Invalidate all user list caches
     */
    private function invalidateUserListCache()
    {
        CacheHelper::flushTags([CacheConstants::TAG_USER_LIST]);
    }
}
