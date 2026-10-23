@props(['value' => 0, 'max' => 100, 'showLabel' => true, 'color' => 'primary'])

@php
    $percent = $max > 0 ? min(100, max(0, ($value / $max) * 100)) : 0;
    $fillClass = match ($color) {
        'gold' => 'bg-warning',
        default => 'bg-primary',
    };
@endphp

<div>
    <div class="progress" role="progressbar" aria-valuenow="{{ round($percent) }}" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar {{ $fillClass }}" style="width: {{ $percent }}%"></div>
    </div>

    @if ($showLabel)
        <p class="small text-body-secondary mt-1 mb-0">
            {{ number_format($value) }} / {{ number_format($max) }}
        </p>
    @endif
</div>
