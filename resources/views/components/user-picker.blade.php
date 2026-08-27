@props(['users', 'model', 'placeholder' => 'Não atribuído'])

@php
    $options = $users->map(fn ($u) => [
        'id' => $u->id,
        'name' => $u->name,
        'initials' => $u->initials(),
        'photo' => $u->person?->foto_path ? \Illuminate\Support\Facades\Storage::url($u->person->foto_path) : null,
        'title' => $u->selectedTitle?->name,
    ])->values();
@endphp

<div
    x-data="{
        open: false,
        selected: @entangle($model),
        options: @js($options),
        get selectedOption() { return this.options.find(o => o.id === this.selected) ?? null },
    }"
    class="relative"
>
    <button type="button" @click="open = !open" @click.outside="open = false"
        class="flex w-full items-center gap-2 rounded-lg border border-line bg-card px-3 py-2 text-left text-sm hover:bg-line/10">
        <template x-if="selectedOption">
            <span class="flex min-w-0 items-center gap-2">
                <template x-if="selectedOption && selectedOption.photo">
                    <img :src="selectedOption.photo" class="h-7 w-7 shrink-0 rounded-full object-cover">
                </template>
                <template x-if="selectedOption && !selectedOption.photo">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-[10px] font-semibold text-white" x-text="selectedOption.initials"></span>
                </template>
                <span class="truncate text-ink" x-text="selectedOption ? selectedOption.name : ''"></span>
            </span>
        </template>
        <span x-show="!selectedOption" class="text-ink-muted">{{ $placeholder }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto h-4 w-4 shrink-0 text-ink-muted"><path d="M6 9l6 6 6-6" /></svg>
    </button>

    <div x-show="open" x-cloak x-transition
        class="absolute z-20 mt-1 max-h-72 w-full min-w-[16rem] overflow-y-auto rounded-lg border border-line bg-card py-1 shadow-lg">
        <button type="button" @click="selected = null; open = false"
            class="flex w-full items-center px-3 py-2 text-left text-sm text-ink-muted hover:bg-line/20">
            {{ $placeholder }}
        </button>

        <template x-for="option in options" :key="option.id">
            <button type="button" @click="selected = option.id; open = false"
                class="flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-line/20"
                :class="{ 'bg-primary/10': option.id === selected }">
                <template x-if="option.photo">
                    <img :src="option.photo" class="h-8 w-8 shrink-0 rounded-full object-cover">
                </template>
                <template x-if="!option.photo">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-semibold text-white" x-text="option.initials"></span>
                </template>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-medium text-ink" x-text="option.name"></span>
                    <template x-if="option.title">
                        <span class="block truncate text-xs text-gold" x-text="option.title"></span>
                    </template>
                </span>
            </button>
        </template>
    </div>
</div>
