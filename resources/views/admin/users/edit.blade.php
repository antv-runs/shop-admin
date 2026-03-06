@extends('admin.layouts.master')

@section('title', 'Edit User')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Edit User</h2>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('email') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Password (leave blank to keep current)</label>
                <input type="password" name="password" class="mt-1 block w-full border rounded px-3 py-2">
                @error('password') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium">Role</label>
                <select name="role" class="mt-1 block w-full border rounded px-3 py-2">
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ (old('role', optional($user->role)->value) === $key) ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600">Back</a>
            </div>
        </form>
    </div>
@endsection
