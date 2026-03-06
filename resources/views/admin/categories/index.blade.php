@extends('admin.layouts.master')

@section('title', 'Categories')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">New Category</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-2 items-end">
            <input type="text" name="search" placeholder="Search name..." value="{{ request('search') }}" class="border rounded px-2 py-1 text-sm">
            <select name="status" class="border rounded px-2 py-1 text-sm">
                <option value="{{ \App\Enums\ItemStatus::ACTIVE->value }}" {{ request('status', \App\Enums\ItemStatus::ACTIVE->value) === \App\Enums\ItemStatus::ACTIVE->value ? 'selected' : '' }}>Active</option>
                <option value="{{ \App\Enums\ItemStatus::DELETED->value }}" {{ request('status') === \App\Enums\ItemStatus::DELETED->value ? 'selected' : '' }}>Deleted</option>
                <option value="{{ \App\Enums\ItemStatus::ALL->value }}" {{ request('status') === \App\Enums\ItemStatus::ALL->value ? 'selected' : '' }}>All</option>
            </select>
            <select name="sort_by" class="border rounded px-2 py-1 text-sm">
                <option value="id" {{ request('sort_by') === 'id' ? 'selected' : '' }}>ID</option>
                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created</option>
            </select>
            <select name="sort_order" class="border rounded px-2 py-1 text-sm">
                <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Desc</option>
                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Asc</option>
            </select>
            <select name="per_page" class="border rounded px-2 py-1 text-sm">
                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Filter</button>
            <a href="{{ route('admin.categories.index') }}" class="px-3 py-1 bg-gray-400 text-white rounded text-sm">Reset</a>
        </form>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    @if(request('status') === \App\Enums\ItemStatus::DELETED->value)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deleted At</th>
                    @else
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    @endif
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $category->slug }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($category->description, 80) }}</td>
                    @if(request('status') === \App\Enums\ItemStatus::DELETED->value)
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $category->deleted_at?->format('M d, Y H:i') }}</td>
                    @else
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $category->created_at?->format('M d, Y') }}</td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if(method_exists($category, 'trashed') && $category->trashed())
                            <form action="{{ route('admin.categories.restore', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 mr-3">Restore</button>
                            </form>

                            <form action="{{ route('admin.categories.forceDelete', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Delete Forever</button>
                            </form>
                        @else
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>

@endsection
