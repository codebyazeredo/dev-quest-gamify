@props(['title', 'maxWidth' => 'max-w-md'])

<div x-data="{ show: false }" x-init="show = true" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="absolute inset-0 bg-ink/40 backdrop-blur-sm"
    ></div>

    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="relative flex max-h-[85vh] w-full {{ $maxWidth }} flex-col rounded-2xl bg-card shadow-2xl"
    >
        <div class="flex shrink-0 items-start justify-between gap-4 p-6 pb-4">
            <h2 class="text-lg font-bold tracking-tight text-ink">{{ $title }}</h2>
            <button type="button" x-on:click="$dispatch('close-modal')" title="Fechar" aria-label="Fechar" class="shrink-0 rounded-full p-1 text-ink-muted hover:bg-line/30">
                <x-icon name="close" class="h-5 w-5" />
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 pb-6">
            {{ $slot }}
        </div>
    </div>
</div>
