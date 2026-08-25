<x-layouts.guest :title="__('Forgot Password')">
    <h1 class="mb-2 text-lg font-semibold text-gray-800 dark:text-gray-100">Forgot your password?</h1>
    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
        Enter your email and we'll send you a link to reset your password.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
        </div>

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Send password reset link
        </button>
    </form>

    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Back to log in</a>
    </p>
</x-layouts.guest>
