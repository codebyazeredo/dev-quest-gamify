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

        <x-button type="submit">Salvar</x-button>
    </form>
</div>
