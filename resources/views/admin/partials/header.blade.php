<header class="bg-white border-b p-4 flex items-center justify-between">
    <div>
        <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
    </div>

    <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600">{{ auth()->user() ? auth()->user()->name : 'Guest' }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600">Logout</button>
        </form>
    </div>
</header>
