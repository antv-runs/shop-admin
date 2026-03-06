@extends('admin.layouts.master')

@section('title', 'Trashed Categories')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Trashed Categories</h2>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm text-gray-600">Deleted categories</h3>
            <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Back to Categories</a>
        </div>

        <!-- Categories Table -->
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Slug</th>
                    <th class="px-4 py-2 text-left">Deleted At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr class="border-t bg-red-50">
                        <td class="px-4 py-2">{{ $category->id }}</td>
                        <td class="px-4 py-2">{{ $category->name }}</td>
                        <td class="px-4 py-2"><code class="text-sm">{{ $category->slug }}</code></td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $category->deleted_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('admin.categories.restore', $category->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 mr-2">Restore</button>
                            </form>

                            <form action="{{ route('admin.categories.forceDelete', $category->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600" onclick="return confirm('Are you sure? This will permanently delete the category.')">Delete Permanently</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-600">
                            No trashed categories.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
