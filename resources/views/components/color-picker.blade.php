@props(['pairs', 'selected' => null])

<div class="d-flex flex-wrap gap-2">
    @foreach ($pairs as $pair)
        <button
            type="button"
            wire:click="selectColor('{{ $pair['bg'] }}', '{{ $pair['text'] }}')"
            title="{{ $pair['label'] }}"
            aria-label="{{ $pair['label'] }}"
            aria-pressed="{{ $selected === $pair['bg'] ? 'true' : 'false' }}"
            class="btn btn-icon fw-bold border-2 {{ $selected === $pair['bg'] ? 'border-primary shadow-sm' : 'border-light-subtle' }}"
            style="background-color: {{ $pair['bg'] }}; color: {{ $pair['text'] }}; width: 2.5rem; height: 2.5rem;"
        >
            Aa
        </button>
    @endforeach
</div>
