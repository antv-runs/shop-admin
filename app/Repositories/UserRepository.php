<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Enums\ItemStatus;

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
    public function getAll($request, $perPage = 15)
    {
        $perPage = (int)$request->input('per_page', $perPage);
        $users = $this->buildQuery($request)->paginate($perPage);

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
    public function getTrashed($request, $perPage = 15)
    {
        $perPage = (int)$request->input('per_page', $perPage);
        $query = User::onlyTrashed();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('deleted_at')->paginate($perPage);

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
     * Build query with search and filters
     */
    public function buildQuery($request)
    {
        $status = $request->input('status', ItemStatus::ACTIVE->value);

        // Query builder based on status
        if ($status === ItemStatus::DELETED->value) {
            $query = User::onlyTrashed();
        } elseif ($status === ItemStatus::ALL->value) {
            $query = User::withTrashed();
        } else {
            $query = User::query();
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $status !== ItemStatus::DELETED->value) {
            $query->where('role', $request->input('role'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'name', 'email', 'role', 'created_at', 'deleted_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

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
