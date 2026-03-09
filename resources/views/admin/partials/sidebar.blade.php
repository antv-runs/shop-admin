<aside class="w-64 bg-white border-r min-h-screen flex flex-col">
    <div class="p-4 border-b">
        <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold">{{ config('app.name', 'Admin') }}</a>
    </div>

    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 font-semibold' : '' }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.products.*') ? 'bg-gray-100 font-semibold' : '' }}">Products Management</a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 font-semibold' : '' }}">Categories Management</a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 font-semibold' : '' }}">Users Management</a>
            </li>
        </ul>
    </nav>

    <!-- User Profile Section -->
    <div class="p-4 border-t">
        <div class="flex items-center space-x-3 mb-3">
            @if(auth()->user()->profile_image)
                <img src="{{ Storage::url(auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold text-gray-600">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <a href="{{ route('admin.profile.show') }}" class="block w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">View Profile</a>
    </div>
</aside>
