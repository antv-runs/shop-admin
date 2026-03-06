<x-guest-layout>
<section class="min-h-screen flex items-center justify-center px-6 py-8">

    <div class="w-full max-w-md bg-white rounded-lg shadow dark:bg-gray-800 dark:border dark:border-gray-700">

        <div class="p-6 space-y-6 sm:p-8">

            <h1 class="text-xl font-bold text-gray-900 md:text-2xl dark:text-white text-center">
                Reset your password
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                Enter your email and we will send you a reset link.
            </p>

            <!-- Session Status -->
            <x-auth-session-status
                class="text-sm text-green-600 dark:text-green-400"
                :status="session('status')"
            />

            <!-- Validation Errors -->
            <x-auth-validation-errors
                class="text-sm text-red-600 dark:text-red-400"
                :errors="$errors"
            />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Your email
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                               dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="name@company.com"
                    >
                </div>

                <div class="flex items-center justify-between">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                            Back to login
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300
                               font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Send Reset Link
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>
</x-guest-layout>
