<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Contracts\UserServiceInterface;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * Inject UserServiceInterface
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $data = $this->userService->getListData($request);
        $roles = $this->userService->getRoles();

        // Return JSON if requested (API compatibility)
        if ($request->wantsJson() || $request->query('api')) {
            return response()->json($data);
        }

        // Return view for web
        return view('admin.users.index', [
            'users' => $data['data'],
            'pagination' => $data['pagination'],
            'filters' => $data['filters'],
            'roles' => $roles,
            'paginator' => $data['paginator']
        ]);
    }

    public function create()
    {
        $roles = $this->userService->getRoles();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        // Create DTO from validated data
        $dto = CreateUserDTO::fromArray($validated);

        $this->userService->createUser($dto);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User created successfully'], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = $this->userService->getRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            // Create DTO from validated data
            $dto = UpdateUserDTO::fromArray($validated);
            
            $user = $this->userService->updateUser($id, $dto);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'User updated successfully', 'data' => $user]);
            }

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
        } catch (BusinessException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
            return redirect()->route('admin.users.index')->with('error', $e->getMessage());
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $this->userService->deleteUser($id);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'User deleted successfully']);
            }

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
        } catch (BusinessException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
            return redirect()->route('admin.users.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show trashed users
     */
    public function trashed(Request $request)
    {
        $data = $this->userService->getTrashed($request);
        $roles = $this->userService->getRoles();

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.users.trashed', [
            'users' => $data['data'],
            'pagination' => $data['pagination'],
            'paginator' => $data['paginator'],
            'roles' => $roles
        ]);
    }

    /**
     * Restore user
     */
    public function restore($id, Request $request)
    {
        try {
            $user = $this->userService->restoreUser($id);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'User restored successfully', 'data' => $user]);
            }

            return redirect()->route('admin.users.trashed')->with('success', 'User restored successfully');
        } catch (BusinessException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return redirect()->route('admin.users.trashed')->with('error', $e->getMessage());
        }
    }

    /**
     * Force delete user
     */
    public function forceDelete($id, Request $request)
    {
        $this->userService->forceDeleteUser($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User permanently deleted']);
        }

        return redirect()->route('admin.users.trashed')->with('success', 'User permanently deleted');
    }
}

