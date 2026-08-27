@props(['pairs', 'selected' => null])

<div class="flex flex-wrap gap-2">
    @foreach ($pairs as $pair)
        <button
            type="button"
            wire:click="selectColor('{{ $pair['bg'] }}', '{{ $pair['text'] }}')"
            title="{{ $pair['label'] }}"
            aria-label="{{ $pair['label'] }}"
            class="flex h-10 w-10 items-center justify-center rounded-lg border-2 text-xs font-bold {{ $selected === $pair['bg'] ? 'border-primary' : 'border-transparent' }}"
            style="background-color: {{ $pair['bg'] }}; color: {{ $pair['text'] }};"
        >
            Aa
        </button>
    @endforeach
</div>
