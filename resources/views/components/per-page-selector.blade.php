@props(['options' => [10, 25, 50]])

<div class="flex items-center gap-2 text-sm text-ink-muted">
    <label for="perPage">Itens por página</label>
    <select wire:model.live="perPage" id="perPage" class="rounded-lg border border-line bg-card px-2 py-1.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        @foreach ($options as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>
</div>
