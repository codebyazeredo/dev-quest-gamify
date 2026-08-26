@props(['value' => 0, 'max' => 100, 'showLabel' => true, 'color' => 'primary'])

@php
    $percent = $max > 0 ? min(100, max(0, ($value / $max) * 100)) : 0;
    $fillClass = match ($color) {
        'gold' => 'bg-gold',
        default => 'bg-primary',
    };
@endphp

<div>
    <div class="h-3 w-full overflow-hidden rounded-full bg-line/40">
        <div class="h-full rounded-full {{ $fillClass }} transition-[width] duration-500 ease-out" style="width: {{ $percent }}%"></div>
    </div>

    @if ($showLabel)
        <p class="mt-1 text-xs text-ink-muted">
            {{ number_format($value) }} / {{ number_format($max) }}
        </p>
    @endif
</div>
