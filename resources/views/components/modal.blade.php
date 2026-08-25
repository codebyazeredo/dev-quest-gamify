@props(['title'])

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
        @if ($title)
            <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $title }}</h2>
        @endif

        {{ $slot }}
    </div>
</div>
