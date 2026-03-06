@extends('admin.layouts.master')

@section('title', 'Trashed Products')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Trashed Products</h2>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm text-gray-600">Deleted products</h3>
            <a href="{{ route('admin.products.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Back to Products</a>
        </div>

        <!-- Products Table -->
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Category</th>
                    <th class="px-4 py-2 text-left">Price</th>
                    <th class="px-4 py-2 text-left">Deleted At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-t bg-red-50">
                        <td class="px-4 py-2">{{ $product->id }}</td>
                        <td class="px-4 py-2">{{ $product->name }}</td>
                        <td class="px-4 py-2">{{ $product->category?->name ?? 'No category' }}</td>
                        <td class="px-4 py-2">{{ number_format($product->price) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $product->deleted_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 mr-2">Restore</button>
                            </form>

                            <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600" onclick="return confirm('Are you sure? This will permanently delete the product.')">Delete Permanently</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-600">
                            No trashed products.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
@endsection
