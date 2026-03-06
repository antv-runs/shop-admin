@extends('admin.layouts.master')

@section('title', 'Users')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Users Management</h2>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm text-gray-600">List of registered users</h3>
            <a href="{{ route('admin.users.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Create User</a>
        </div>

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 p-4 bg-gray-50 rounded">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Search (Name / Email)</label>
                    <input type="text" name="search" placeholder="Search..."
                           value="{{ $filters['search'] ?? '' }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="{{ \App\Enums\ItemStatus::ACTIVE->value }}" {{ ($filters['status'] ?? \App\Enums\ItemStatus::ACTIVE->value) === \App\Enums\ItemStatus::ACTIVE->value ? 'selected' : '' }}>Active</option>
                        <option value="{{ \App\Enums\ItemStatus::DELETED->value }}" {{ ($filters['status'] ?? \App\Enums\ItemStatus::ACTIVE->value) === \App\Enums\ItemStatus::DELETED->value ? 'selected' : '' }}>Deleted (Disabled)</option>
                        <option value="{{ \App\Enums\ItemStatus::ALL->value }}" {{ ($filters['status'] ?? \App\Enums\ItemStatus::ACTIVE->value) === \App\Enums\ItemStatus::ALL->value ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Role</label>
                    <select name="role" class="w-full border rounded px-3 py-2 text-sm" {{ ($filters['status'] ?? 'active') === 'deleted' ? 'disabled' : '' }}>
                        <option value="">-- All Roles --</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['role'] ?? '') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Sort By</label>
                    <select name="sort_by" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="id" {{ ($filters['sort_by'] ?? 'id') === 'id' ? 'selected' : '' }}>ID</option>
                        <option value="name" {{ ($filters['sort_by'] ?? 'id') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ ($filters['sort_by'] ?? 'id') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="role" {{ ($filters['sort_by'] ?? 'id') === 'role' ? 'selected' : '' }}>Role</option>
                        <option value="created_at" {{ ($filters['sort_by'] ?? 'id') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        @if(($filters['status'] ?? \App\Enums\ItemStatus::ACTIVE->value) === \App\Enums\ItemStatus::DELETED->value)
                            <option value="deleted_at" {{ ($filters['sort_by'] ?? 'id') === 'deleted_at' ? 'selected' : '' }}>Deleted Date</option>
                        @endif
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded text-sm">Search</button>
                    <a href="{{ route('admin.users.index') }}" class="flex-1 px-4 py-2 bg-gray-400 text-white rounded text-sm text-center">Reset</a>
                </div>
            </div>
        </form>

        <!-- Results Info -->
        <div class="mb-3 text-sm text-gray-600">
            @if(($filters['status'] ?? 'active') === 'deleted')
                Showing {{ $pagination['from'] ?? 0 }} to {{ $pagination['to'] ?? 0 }} of {{ $pagination['total'] ?? 0 }} <strong>disabled</strong> users
            @elseif(($filters['status'] ?? 'active') === 'all')
                Showing {{ $pagination['from'] ?? 0 }} to {{ $pagination['to'] ?? 0 }} of {{ $pagination['total'] ?? 0 }} <strong>total</strong> users
            @else
                Showing {{ $pagination['from'] ?? 0 }} to {{ $pagination['to'] ?? 0 }} of {{ $pagination['total'] ?? 0 }} <strong>active</strong> users
            @endif
        </div>

        <!-- Users Table -->
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    @if(($filters['status'] ?? 'active') === 'deleted')
                        <th class="px-4 py-2 text-left">Deleted At</th>
                    @else
                        <th class="px-4 py-2 text-left">Created</th>
                    @endif
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t {{ $user->trashed() ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-2">{{ $user->id }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $user->role->value === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            @if($user->trashed())
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Disabled</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            @if($user->trashed())
                                {{ $user->deleted_at->format('M d, Y H:i') }}
                            @else
                                {{ $user->created_at->format('M d, Y') }}
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($user->trashed())
                                {{-- Restore button for deleted users --}}
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 mr-2 text-sm">Restore</button>
                                </form>

                                {{-- Force delete button --}}
                                <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Permanently delete this user? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm">Delete Forever</button>
                                </form>
                            @else
                                {{-- Edit and delete buttons for active users --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 mr-2 text-sm">Edit</a>

                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Disable this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm">Disable</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-600">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $paginator->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
