@props(['user', 'size' => 'md'])

@php
    $sizeClass = match ($size) {
        'sm' => 'avatar-sm',
        'lg' => 'avatar-lg',
        'xl' => 'avatar-xl',
        default => '',
    };
    $photoPath = $user->person?->foto_path;
@endphp

@if ($photoPath)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($photoPath) }}" alt="" {{ $attributes->merge(['class' => "avatar {$sizeClass}"]) }}>
@else
    <span {{ $attributes->merge(['class' => "avatar {$sizeClass} text-bg-primary"]) }}>{{ $user->initials() }}</span>
@endif
