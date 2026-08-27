@props(['label', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="mt-2">
    <button type="button" @click="open = !open" class="flex w-full items-center justify-between rounded-md px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white/40 hover:text-white/80">
        {{ $label }}
        <x-icon name="chevron-down" class="h-3.5 w-3.5 transition-transform" x-bind:class="{ '-rotate-90': ! open }" />
    </button>

    <div x-show="open" class="flex flex-col gap-1">
        {{ $slot }}
    </div>
</div>
