<div>
    <x-page-header title="Identidade da empresa" :back="route('admin.index')" backLabel="Configurações" />

    <form wire:submit="save" class="max-w-md space-y-4 rounded-xl border border-line bg-card p-5">
        <x-input name="company_name" label="Nome da empresa" wire:model="company_name" placeholder="{{ \App\Models\AppSetting::DEFAULT_NAME }}" />

        <div>
            <label class="block text-sm font-medium text-ink">Logo</label>

            @if ($setting->logoUrl() && ! $logo)
                <div class="mt-2 flex w-full items-center justify-center rounded-lg border border-line bg-surface p-6">
                    <img src="{{ $setting->logoUrl() }}" alt="" class="h-48 max-w-full w-auto">
                </div>
            @endif

            @if ($logo)
                <div class="mt-2 flex w-full items-center justify-center rounded-lg border border-line bg-surface p-6">
                    <img src="{{ $logo->temporaryUrl() }}" alt="" class="h-48 max-w-full w-auto">
                </div>
            @endif

            <input type="file" wire:model="logo" accept="image/*"
                class="mt-2 block w-full text-sm text-ink file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/20">
            @error('logo') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Tamanho da logo na tela de login</label>

            <div class="mt-2 grid grid-cols-3 gap-2">
                @foreach (\App\Enums\LogoSize::cases() as $size)
                    <label class="flex cursor-pointer flex-col items-center gap-1.5 rounded-lg border p-3 text-sm {{ $logo_size === $size->value ? 'border-primary bg-primary/5 text-primary' : 'border-line text-ink-muted hover:bg-surface' }}">
                        <input type="radio" wire:model.live="logo_size" value="{{ $size->value }}" class="sr-only">
                        <span class="flex h-8 items-center">
                            <span class="rounded bg-current" style="{{ match ($size) {
                                \App\Enums\LogoSize::SMALL => 'width: 1.1rem; height: 0.9rem;',
                                \App\Enums\LogoSize::MEDIUM => 'width: 1.6rem; height: 1.3rem;',
                                \App\Enums\LogoSize::LARGE => 'width: 2.2rem; height: 1.8rem;',
                            } }} opacity: 0.25;"></span>
                        </span>
                        <span class="font-medium">{{ $size->label() }}</span>
                    </label>
                @endforeach
            </div>
            @error('logo_size') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <x-button type="submit">Salvar</x-button>
    </form>
</div>
