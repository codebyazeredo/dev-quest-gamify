@props(['amount'])

<x-badge :color="$amount >= 0 ? 'success' : 'danger'">
    {{ $amount >= 0 ? '+' : '' }}{{ number_format($amount) }} XP
</x-badge>
