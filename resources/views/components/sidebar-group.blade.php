@props(['label', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="mt-3 border-t border-white/10 pt-3 first:mt-2 first:border-t-0 first:pt-0">
    <button type="button" @click="open = !open" class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white/40 transition-colors hover:text-white/80">
        {{ $label }}
        <x-icon name="chevron-down" class="h-3.5 w-3.5 shrink-0 transition-transform" x-bind:class="{ '-rotate-90': ! open }" />
    </button>

    <div x-show="open" x-cloak class="mt-1 flex flex-col gap-1">
        {{ $slot }}
    </div>
</div>
