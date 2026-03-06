@extends('admin.layouts.master')

@section('title', 'Edit Category')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Edit Category</h1>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4 max-w-lg">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="mt-1 block w-full border-gray-300 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" class="mt-1 block w-full border-gray-300 rounded">{{ old('description', $category->description) }}</textarea>
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('admin.categories.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
</div>

@endsection
