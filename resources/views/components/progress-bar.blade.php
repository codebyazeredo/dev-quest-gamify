@props(['value' => 0, 'max' => 100, 'showLabel' => true])

@php
    $percent = $max > 0 ? min(100, max(0, ($value / $max) * 100)) : 0;
@endphp

<div>
    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
        <div class="h-full rounded-full bg-indigo-600 transition-[width] duration-500 ease-out" style="width: {{ $percent }}%"></div>
    </div>

    @if ($showLabel)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ number_format($value) }} / {{ number_format($max) }}
        </p>
    @endif
</div>
