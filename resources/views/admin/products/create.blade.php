@extends('admin.layouts.master')

@section('title', 'Add Product')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Add Product</h2>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Images:</label>
                <div id="preview-list" class="mb-2 flex flex-wrap gap-2"></div>
                <input type="file" name="images[]" id="images" accept="image/*" multiple class="mt-1 block w-full">
                @error('images') <div class="text-danger text-red-600">{{ $message }}</div> @enderror
                @foreach ($errors->get('images.*') as $messages)
                    @foreach ($messages as $message)
                        <div class="text-danger text-red-600">{{ $message }}</div>
                    @endforeach
                @endforeach
            </div>

            <div>
                <label class="block font-medium">Name:</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Category:</label>
                <select name="category_id" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Price:</label>
                <input type="text" name="price" value="{{ old('price') }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('price') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Compare Price:</label>
                <input type="text" name="compare_price" value="{{ old('compare_price') }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('compare_price') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Currency:</label>
                <input type="text" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="mt-1 block w-full border rounded px-3 py-2 uppercase">
                @error('currency') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="inline-flex items-center gap-2 font-medium">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    Active
                </label>
                @error('is_active') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Description:</label>
                <textarea name="description" class="mt-1 block w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Details:</label>
                <textarea name="details" class="mt-1 block w-full border rounded px-3 py-2">{{ old('details') }}</textarea>
                @error('details') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="colors" class="block text-sm font-medium text-gray-700">Colors</label>
                <input type="text" name="colors_input" id="colors"
                       placeholder="e.g. Red, Blue, Green"
                       value="{{ old('colors') ? implode(', ', old('colors')) : '' }}"
                       class="mt-1 block w-full border rounded px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Separate values with commas</p>
                @error('colors') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sizes" class="block text-sm font-medium text-gray-700">Sizes</label>
                <input type="text" name="sizes_input" id="sizes"
                       placeholder="e.g. S, M, L, XL"
                       value="{{ old('sizes') ? implode(', ', old('sizes')) : '' }}"
                       class="mt-1 block w-full border rounded px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Separate values with commas</p>
                @error('sizes') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600">Back</a>
            </div>
        </form>
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
