<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\UserFilterDTO;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID
     */
    public function findById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Get all users with optional filters
     */
    public function getAll(UserFilterDTO $filter)
    {
        $users = $this->buildQuery($filter)->paginate($filter->perPage, ['*'], 'page', $filter->page);

        return $users;
    }

    /**
     * Create a new user
     */
    public function create(array $data)
    {
        return User::create($data);
    }

    /**
     * Update a user
     */
    public function update($user, array $data)
    {
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user (soft delete)
     */
    public function delete($id)
    {
        $user = $this->findById($id);
        $user->delete();
        return true;
    }

    /**
     * Get trashed users
     */
    public function getTrashed(UserFilterDTO $filter)
    {
        $query = User::onlyTrashed();

        // Search by name or email
        if (!empty($filter->search)) {
            $search = $filter->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('deleted_at')->paginate($filter->perPage, ['*'], 'page', $filter->page);

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
            'paginator' => $users
        ];
    }

    /**
     * Restore a user
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            throw new \Exception('User is not deleted.');
        }

        $user->restore();
        return $user;
    }

    /**
     * Force delete a user
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();
    }

    /**
     * Build query with search filter
     */
    private function buildQuery(UserFilterDTO $filter)
    {
        $query = User::query();

        // Search by name or email
        if (!empty($filter->search)) {
            $search = $filter->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        return $query;
    }

    /**
     * Paginate users
     */
    public function paginate($perPage = 15)
    {
        return User::paginate($perPage);
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
