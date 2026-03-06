<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Hello, {{ auth()->user() ? auth()->user()->name : 'User' }}!</h2>
                    <p class="mt-2 text-gray-600">You are logged in. This page is for regular users (no access to /admin).</p>

                    <div class="mt-4">
                        <h3 class="font-medium">Available products</h3>
                        @if(isset($products) && $products->count())
                            <div class="mt-2 bg-white p-4 rounded shadow">
                                <table class="min-w-full table-auto">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left">Name</th>
                                            <th class="px-4 py-2 text-left">Category</th>
                                            <th class="px-4 py-2 text-left">Price</th>
                                            <th class="px-4 py-2 text-left">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr class="border-t">
                                                <td class="px-4 py-2">{{ $product->name }}</td>
                                                <td class="px-4 py-2">{{ $product->category?->name ?? 'No category' }}</td>
                                                <td class="px-4 py-2">{{ number_format($product->price) }}</td>
                                                <td class="px-4 py-2">{{ $product->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-4">{{ $products->links() }}</div>
                            </div>
                        @else
                            <p class="mt-2 text-gray-600">No products available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
