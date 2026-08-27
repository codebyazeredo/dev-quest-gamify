@props(['label' => null, 'name', 'rows' => 3])

@php
    $id = $attributes->get('id') ?: $name;
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <textarea
        id="{{ $id }}"
        rows="{{ $rows }}"
        {{ $attributes->whereDoesntStartWith(['id', 'rows'])->merge(['class' => 'mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30']) }}
    ></textarea>

    @error($name) <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
</div>
