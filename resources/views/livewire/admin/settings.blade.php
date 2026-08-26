<div>
    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Configurações</h1>

    <form wire:submit="save" class="max-w-md space-y-4 rounded-xl border border-line bg-card p-5">
        <div>
            <label class="block text-sm font-medium text-ink">Nome da empresa</label>
            <input type="text" wire:model="company_name" placeholder="Dev Quest"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('company_name') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Logo</label>

            @if ($setting->logo_path && ! $logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" alt="" class="mt-2 h-12 w-auto">
            @endif

            @if ($logo)
                <img src="{{ $logo->temporaryUrl() }}" alt="" class="mt-2 h-12 w-auto">
            @endif

            <input type="file" wire:model="logo" accept="image/*"
                class="mt-2 block w-full text-sm text-ink file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/20">
            @error('logo') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
            Salvar
        </button>
    </form>
</div>
