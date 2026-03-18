@extends('admin.layouts.master')

@section('title', 'Reviews')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Reviews Management</h2>
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-sm text-gray-600">List of product reviews</h3>
            <a href="{{ route('admin.reviews.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Add Review</a>
        </div>

        @if(session('success'))
            <p class="mb-4 text-green-600">{{ session('success') }}</p>
        @endif

        <form method="GET" action="{{ route('admin.reviews.index') }}" class="mb-6 p-4 bg-gray-50 rounded">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Search by User Email</label>
                    <input
                        type="text"
                        name="search"
                        placeholder="user@example.com"
                        value="{{ $filters['search'] ?? '' }}"
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Product</label>
                    <select name="product_id" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (string) ($filters['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Rating</label>
                    <select name="rating" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="" {{ ($filters['rating'] ?? '') === '' ? 'selected' : '' }}>All</option>
                        <option value="1" {{ ($filters['rating'] ?? '') === '1' ? 'selected' : '' }}>1</option>
                        <option value="1.5" {{ ($filters['rating'] ?? '') === '1.5' ? 'selected' : '' }}>1.5</option>
                        <option value="2" {{ ($filters['rating'] ?? '') === '2' ? 'selected' : '' }}>2</option>
                        <option value="2.5" {{ ($filters['rating'] ?? '') === '2.5' ? 'selected' : '' }}>2.5</option>
                        <option value="3" {{ ($filters['rating'] ?? '') === '3' ? 'selected' : '' }}>3</option>
                        <option value="3.5" {{ ($filters['rating'] ?? '') === '3.5' ? 'selected' : '' }}>3.5</option>
                        <option value="4" {{ ($filters['rating'] ?? '') === '4' ? 'selected' : '' }}>4</option>
                        <option value="4.5" {{ ($filters['rating'] ?? '') === '4.5' ? 'selected' : '' }}>4.5</option>
                        <option value="5" {{ ($filters['rating'] ?? '') === '5' ? 'selected' : '' }}>5</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Sort</label>
                    <select name="sort" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="latest" {{ ($filters['sort'] ?? 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ ($filters['sort'] ?? 'latest') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="highest" {{ ($filters['sort'] ?? 'latest') === 'highest' ? 'selected' : '' }}>Highest rating</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded text-sm">Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="flex-1 px-4 py-2 bg-gray-400 text-white rounded text-sm text-center">Reset</a>
                </div>
            </div>
        </form>

        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">User</th>
                    <th class="px-4 py-2 text-left">Rating</th>
                    <th class="px-4 py-2 text-left">Comment</th>
                    <th class="px-4 py-2 text-left">Verified</th>
                    <th class="px-4 py-2 text-left">Created At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $review->id }}</td>
                        <td class="px-4 py-2">{{ $review->product?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $review->user?->email ?? 'Guest' }}</td>
                        <td class="px-4 py-2">{{ number_format((float) $review->rating, 1) }}</td>
                        <td class="px-4 py-2">{{ \Illuminate\Support\Str::limit($review->comment, 80) }}</td>
                        <td class="px-4 py-2">{{ $review->is_verified ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $review->created_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.reviews.edit', $review->id) }}" class="text-indigo-600 text-sm mr-2">Edit</a>
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                @foreach(request()->query() as $queryKey => $queryValue)
                                    <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                                @endforeach
                                <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-600">No reviews found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection
