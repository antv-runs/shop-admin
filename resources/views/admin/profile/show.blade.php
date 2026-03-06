@extends('admin.layouts.master')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Profile Header -->
            <div class="flex items-center mb-8 pb-8 border-b">
                <div class="w-24 h-24 mr-6">
                    @if($user->profile_image)
                        <img src="{{ Storage::disk('minio')->url($user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover border-4 border-blue-500">
                    @else
                        <div class="w-full h-full rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-300">
                            <span class="text-2xl font-bold text-gray-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">{{ $user->role->label() }}</span>
                    </p>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="text-gray-600 mt-1">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Bio</label>
                        <p class="text-gray-600 mt-1">{{ $user->bio ?? 'No bio added yet.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4 pt-4 border-t">
                <a href="{{ route('admin.profile.edit') }}" class="inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    Edit Profile
                </a>

                <a href="{{ route('admin.dashboard') }}" class="inline-block px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
