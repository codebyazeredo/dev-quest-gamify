@props(['amount'])

<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $amount >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
    {{ $amount >= 0 ? '+' : '' }}{{ number_format($amount) }} XP
</span>
