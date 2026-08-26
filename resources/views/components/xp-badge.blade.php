@props(['amount'])

<x-badge :color="$amount >= 0 ? 'forest' : 'terracotta'">
    {{ $amount >= 0 ? '+' : '' }}{{ number_format($amount) }} XP
</x-badge>
