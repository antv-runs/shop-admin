@extends('admin.layouts.master')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Profile Image Section -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Profile Picture</h2>

                    <div class="flex items-start space-x-6">
                        <div class="w-24 h-24">
                            @if($user->profile_image)
                                <img src="{{ Storage::disk(config('filesystems.default'))->url($user->profile_image) }}" alt="{{ $user->name }}" id="preview" class="w-full h-full rounded-full object-cover border-4 border-blue-500">
                            @else
                                <div id="preview" class="w-full h-full rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-300">
                                    <span class="text-2xl font-bold text-gray-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Image</label>
                            <input type="file" name="profile_image" id="profile_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF up to 2MB</p>

                            @if($user->profile_image)
                                <form action="{{ route('admin.profile.deleteImage') }}" method="POST" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:text-red-700" onclick="return confirm('Remove profile picture?')">
                                        Remove Picture
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h2>

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="mb-6">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea id="bio" name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-4 pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                        Save Changes
                    </button>

                    <a href="{{ route('admin.profile.show') }}" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full rounded-full object-cover border-4 border-blue-500">';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
