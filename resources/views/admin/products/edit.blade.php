@extends('admin.layouts.master')

@section('title', 'Edit Product')

@section('content')
    <div class="space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Basic Product Information</h2>

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium">Name:</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full border rounded px-3 py-2">
                    @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Price:</label>
                    <input type="text" name="price" value="{{ old('price', $product->price) }}" class="mt-1 block w-full border rounded px-3 py-2">
                    @error('price') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Compare Price:</label>
                    <input type="text" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" class="mt-1 block w-full border rounded px-3 py-2">
                    @error('compare_price') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Description:</label>
                    <textarea name="description" class="mt-1 block w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Details:</label>
                    <textarea name="details" class="mt-1 block w-full border rounded px-3 py-2">{{ old('details', $product->details) }}</textarea>
                    @error('details') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="colors" class="block text-sm font-medium text-gray-700">Colors</label>
                    <input type="text" name="colors_input" id="colors"
                           placeholder="e.g. Red, Blue, Green"
                           value="{{ old('colors', $product->colors ? implode(', ', $product->colors) : '') }}"
                           class="mt-1 block w-full border rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Separate values with commas</p>
                    @error('colors') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sizes" class="block text-sm font-medium text-gray-700">Sizes</label>
                    <input type="text" name="sizes_input" id="sizes"
                           placeholder="e.g. S, M, L, XL"
                           value="{{ old('sizes',  $product->sizes  ? implode(', ', $product->sizes)  : '') }}"
                           class="mt-1 block w-full border rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Separate values with commas</p>
                    @error('sizes') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Category:</label>
                    <select name="category_id" class="mt-1 block w-full border rounded px-3 py-2">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Currency:</label>
                    <input type="text" name="currency" value="{{ old('currency', $product->currency ?? 'USD') }}" maxlength="3" class="mt-1 block w-full border rounded px-3 py-2 uppercase">
                    @error('currency') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 font-medium">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                    @error('is_active') <p class="text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Basic Information</button>
                    <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600">Back</a>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Product Images</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Current Gallery</p>

                    @if($product->images->isEmpty())
                        <p class="text-sm text-gray-500">No images uploaded yet.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($product->images as $image)
                                <div class="border rounded p-3">
                                    <img src="{{ Storage::url($image->image_url) }}" alt="Product Image {{ $image->id }}" class="h-28 w-full object-cover rounded">
                                    <div class="mt-2 text-sm text-gray-700">Image ID: {{ $image->id }}</div>
                                    <div class="mt-1 text-sm">
                                        @if($image->is_primary)
                                            <span class="text-green-600 font-medium">Primary</span>
                                        @else
                                            <span class="text-gray-500">Secondary</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if(!$image->is_primary)
                                            <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="price" value="{{ $product->price }}">
                                                <input type="hidden" name="compare_price" value="{{ $product->compare_price }}">
                                                <input type="hidden" name="description" value="{{ $product->description }}">
                                                <input type="hidden" name="details" value="{{ $product->details }}">
                                                <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                                <input type="hidden" name="currency" value="{{ $product->currency ?? 'USD' }}">
                                                <input type="hidden" name="is_active" value="{{ $product->is_active ? 1 : 0 }}">
                                                <input type="hidden" name="image_action" value="set_primary">
                                                <input type="hidden" name="image_id" value="{{ $image->id }}">
                                                <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded">
                                                    Set Primary
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$loop->first)
                                            <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="price" value="{{ $product->price }}">
                                                <input type="hidden" name="compare_price" value="{{ $product->compare_price }}">
                                                <input type="hidden" name="description" value="{{ $product->description }}">
                                                <input type="hidden" name="details" value="{{ $product->details }}">
                                                <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                                <input type="hidden" name="currency" value="{{ $product->currency ?? 'USD' }}">
                                                <input type="hidden" name="is_active" value="{{ $product->is_active ? 1 : 0 }}">
                                                <input type="hidden" name="image_action" value="move_left">
                                                <input type="hidden" name="image_id" value="{{ $image->id }}">
                                                <button type="submit" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded">
                                                    <- Move Left
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$loop->last)
                                            <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="price" value="{{ $product->price }}">
                                                <input type="hidden" name="compare_price" value="{{ $product->compare_price }}">
                                                <input type="hidden" name="description" value="{{ $product->description }}">
                                                <input type="hidden" name="details" value="{{ $product->details }}">
                                                <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                                <input type="hidden" name="currency" value="{{ $product->currency ?? 'USD' }}">
                                                <input type="hidden" name="is_active" value="{{ $product->is_active ? 1 : 0 }}">
                                                <input type="hidden" name="image_action" value="move_right">
                                                <input type="hidden" name="image_id" value="{{ $image->id }}">
                                                <button type="submit" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded">
                                                    Move Right ->
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name" value="{{ $product->name }}">
                                            <input type="hidden" name="price" value="{{ $product->price }}">
                                            <input type="hidden" name="compare_price" value="{{ $product->compare_price }}">
                                            <input type="hidden" name="description" value="{{ $product->description }}">
                                            <input type="hidden" name="details" value="{{ $product->details }}">
                                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                            <input type="hidden" name="currency" value="{{ $product->currency ?? 'USD' }}">
                                            <input type="hidden" name="is_active" value="{{ $product->is_active ? 1 : 0 }}">
                                            <input type="hidden" name="image_action" value="delete_image">
                                            <input type="hidden" name="image_id" value="{{ $image->id }}">
                                            <button type="submit" class="px-3 py-1 text-sm bg-red-600 text-white rounded">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="name" value="{{ old('name', $product->name) }}">
                    <input type="hidden" name="price" value="{{ old('price', $product->price) }}">
                    <input type="hidden" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}">
                    <input type="hidden" name="description" value="{{ old('description', $product->description) }}">
                    <input type="hidden" name="details" value="{{ old('details', $product->details) }}">
                    <input type="hidden" name="category_id" value="{{ old('category_id', $product->category_id) }}">
                    <input type="hidden" name="currency" value="{{ old('currency', $product->currency ?? 'USD') }}">
                    <input type="hidden" name="is_active" value="{{ old('is_active', $product->is_active ? 1 : 0) }}">

                    <div>
                        <label class="block font-medium">Upload Additional Images:</label>
                        <div id="preview-list" class="mb-2 flex flex-wrap gap-2"></div>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple class="mt-1 block w-full">
                        @error('images') <div class="text-danger text-red-600">{{ $message }}</div> @enderror
                        @foreach ($errors->get('images.*') as $messages)
                            @foreach ($messages as $message)
                                <div class="text-danger text-red-600">{{ $message }}</div>
                            @endforeach
                        @endforeach
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Upload Images</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('images').addEventListener('change', function(event) {
            const previewList = document.getElementById('preview-list');

            previewList.innerHTML = '';

            const files = Array.from(event.target.files || []);

            if (!files.length) {
                return;
            }

            files.forEach(function(file) {
                const reader = new window.FileReader();
                reader.onload = function(e) {
                    const image = document.createElement('img');
                    image.src = e.target.result;
                    image.alt = file.name;
                    image.className = 'h-24 w-auto rounded object-cover';
                    previewList.appendChild(image);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
