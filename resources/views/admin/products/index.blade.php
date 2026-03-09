@extends('admin.layouts.master')

@section('content')

<h1 class="text-2xl font-semibold">Product List</h1>

@if(session('success'))
    <p class="text-green-600">{{ session('success') }}</p>
@endif

<div class="flex justify-between items-center gap-4">
    <a href="{{ route('admin.products.create') }}" class="inline-block px-3 py-2 bg-indigo-600 text-white rounded">Add Product</a>

    <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2 items-end">
        <input type="text" name="search" placeholder="Search name..." value="{{ request('search') }}" class="border rounded px-2 py-1 text-sm">
        <select name="status" class="border rounded px-2 py-1 text-sm">
            <option value="{{ \App\Enums\ItemStatus::ACTIVE->value }}" {{ request('status', \App\Enums\ItemStatus::ACTIVE->value) === \App\Enums\ItemStatus::ACTIVE->value ? 'selected' : '' }}>Active</option>
            <option value="{{ \App\Enums\ItemStatus::DELETED->value }}" {{ request('status') === \App\Enums\ItemStatus::DELETED->value ? 'selected' : '' }}>Deleted</option>
            <option value="{{ \App\Enums\ItemStatus::ALL->value }}" {{ request('status') === \App\Enums\ItemStatus::ALL->value ? 'selected' : '' }}>All</option>
        </select>
        <select name="category_id" class="border rounded px-2 py-1 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="sort_by" class="border rounded px-2 py-1 text-sm">
            <option value="id" {{ request('sort_by') === 'id' ? 'selected' : '' }}>ID</option>
            <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
            <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Price</option>
            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created</option>
        </select>
        <select name="sort_order" class="border rounded px-2 py-1 text-sm">
            <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Desc</option>
            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Asc</option>
        </select>
        <select name="per_page" class="border rounded px-2 py-1 text-sm">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
        <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Filter</button>
        <a href="{{ route('admin.products.index') }}" class="px-3 py-1 bg-gray-400 text-white rounded text-sm">Reset</a>
    </form>
</div>

<table class="min-w-full mt-4 table-auto">
    <thead>
    <tr>
        <th class="px-4 py-2 text-left">ID</th>
        <th class="px-4 py-2 text-left">Image</th>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-left">Price</th>
        <th class="px-4 py-2 text-left">Category</th>
        <th class="px-4 py-2 text-left">Description</th>
        @if(request('status') === \App\Enums\ItemStatus::DELETED->value)
            <th class="px-4 py-2 text-left">Deleted At</th>
        @else
            <th class="px-4 py-2 text-left">Created</th>
        @endif
        <th class="px-4 py-2 text-left">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
    <tr class="border-t">
        <td class="px-4 py-2">{{ $product->id }}</td>
        <td class="px-4 py-2">
            @if($product->image)
                <a href="{{ $product->image_url }}" target="_blank">
                    <img src="{{ $product->image_url }}" alt="img" class="h-12 w-12 object-cover rounded">
                </a>
            @else
                <span class="text-sm text-gray-500">—</span>
            @endif
        </td>
        <td class="px-4 py-2">{{ $product->name }}</td>
        <td class="px-4 py-2">{{ number_format($product->price) }}</td>
        <td class="px-4 py-2">{{ $product->category?->name ?? 'No category' }}</td>
        <td class="px-4 py-2">{{ $product->description }}</td>
        @if(request('status') === \App\Enums\ItemStatus::DELETED->value)
            <td class="px-4 py-2 text-sm text-gray-600">{{ $product->deleted_at?->format('M d, Y H:i') }}</td>
        @else
            <td class="px-4 py-2 text-sm text-gray-600">{{ $product->created_at?->format('M d, Y') }}</td>
        @endif
        <td class="px-4 py-2">
            @if(method_exists($product, 'trashed') && $product->trashed())
                <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-green-600 mr-2 text-sm">Restore</button>
                </form>

                <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Permanently delete this product? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 text-sm">Delete Forever</button>
                </form>
            @else
                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-indigo-600 mr-2">Edit</a>

                <form action="{{ route('admin.products.destroy', $product->id) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600">Delete</button>
                </form>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

{{ $products->links() }}

@endsection
