@props(['model', 'icons'])

<div class="flex flex-wrap gap-2">
    @foreach ($icons as $iconName)
        <label class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-lg border border-line text-ink-muted hover:bg-line/20 has-[:checked]:border-primary has-[:checked]:bg-primary/10 has-[:checked]:text-primary">
            <input type="radio" wire:model="{{ $model }}" value="{{ $iconName }}" class="sr-only">
            <x-icon :name="$iconName" class="h-6 w-6" />
        </label>
    @endforeach
</div>
