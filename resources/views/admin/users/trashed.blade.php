@extends('admin.layouts.master')

@section('title', 'Trashed Users')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Trashed Users</h2>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm text-gray-600">Deleted users</h3>
            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Back to Users</a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.users.trashed') }}" class="mb-6 p-4 bg-gray-50 rounded">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Search (Name / Email)</label>
                    <input type="text" name="search" placeholder="Search..."
                           value="{{ request('search') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded text-sm">Search</button>
                    <a href="{{ route('admin.users.trashed') }}" class="flex-1 px-4 py-2 bg-gray-400 text-white rounded text-sm text-center">Reset</a>
                </div>
            </div>
        </form>

        <!-- Results Info -->
        <div class="mb-3 text-sm text-gray-600">
            Showing {{ $pagination['from'] ?? 0 }} to {{ $pagination['to'] ?? 0 }} of {{ $pagination['total'] ?? 0 }} trashed users
        </div>

        <!-- Users Table -->
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-left">Deleted At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t bg-red-50">
                        <td class="px-4 py-2">{{ $user->id }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $user->role->value === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $user->deleted_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 mr-2">Restore</button>
                            </form>

                            <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600" onclick="return confirm('Are you sure? This will permanently delete the user.')">Delete Permanently</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-600">
                            No trashed users.
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
