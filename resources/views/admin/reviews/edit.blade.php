@extends('admin.layouts.master')

@section('title', 'Edit Review')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Edit Review</h2>

        <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Product</label>
                <select name="product_id" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ (string) old('product_id', $review->product_id) === (string) $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">User (optional)</label>
                <select name="user_id" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">Guest</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string) old('user_id', $review->user_id) === (string) $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Rating</label>
                <select name="rating" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">-- Select Rating --</option>
                    @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5] as $rating)
                        <option value="{{ $rating }}" {{ (string) old('rating', $review->rating) === (string) $rating ? 'selected' : '' }}>
                            {{ $rating }}
                        </option>
                    @endforeach
                </select>
                @error('rating') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Comment</label>
                <textarea name="comment" rows="4" class="mt-1 block w-full border rounded px-3 py-2">{{ old('comment', $review->comment) }}</textarea>
                @error('comment') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="inline-flex items-center gap-2 font-medium">
                    <input type="hidden" name="is_verified" value="0">
                    <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $review->is_verified) ? 'checked' : '' }}>
                    Verified
                </label>
                @error('is_verified') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
                <a href="{{ route('admin.reviews.index') }}" class="text-sm text-gray-600">Back</a>
            </div>
        </form>
    </div>
@endsection
