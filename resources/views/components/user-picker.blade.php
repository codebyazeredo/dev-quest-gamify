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
    class="position-relative"
>
    <button type="button" @click="open = !open" @click.outside="open = false"
        class="btn btn-outline-secondary bg-body text-body w-100 d-flex align-items-center gap-2 text-start">
        <template x-if="selectedOption">
            <span class="d-flex align-items-center gap-2 min-w-0">
                <template x-if="selectedOption && selectedOption.photo">
                    <img :src="selectedOption.photo" alt="" class="avatar avatar-sm">
                </template>
                <template x-if="selectedOption && !selectedOption.photo">
                    <span class="avatar avatar-sm text-bg-primary" x-text="selectedOption.initials"></span>
                </template>
                <span class="text-truncate" x-text="selectedOption ? selectedOption.name : ''"></span>
            </span>
        </template>
        <span x-show="!selectedOption" class="text-body-secondary">{{ $placeholder }}</span>
        <i class="bi bi-chevron-expand ms-auto text-body-secondary" aria-hidden="true"></i>
    </button>

    <div x-show="open" x-cloak x-transition class="dropdown-menu show w-100 mt-1 shadow overflow-y-auto" style="max-height: 18rem;">
        <button type="button" @click="selected = null; open = false" class="dropdown-item text-body-secondary">
            {{ $placeholder }}
        </button>

        <template x-for="option in options" :key="option.id">
            <button type="button" @click="selected = option.id; open = false"
                class="dropdown-item d-flex align-items-center gap-2 py-2"
                :class="{ 'bg-primary-subtle text-body': option.id === selected }">
                <template x-if="option.photo">
                    <img :src="option.photo" alt="" class="avatar">
                </template>
                <template x-if="!option.photo">
                    <span class="avatar text-bg-primary" x-text="option.initials"></span>
                </template>
                <span class="min-w-0">
                    <span class="d-block fw-medium text-truncate" x-text="option.name"></span>
                    <template x-if="option.title">
                        <span class="d-block small text-warning-emphasis text-truncate" x-text="option.title"></span>
                    </template>
                </span>
            </button>
        </template>
    </div>
</div>
