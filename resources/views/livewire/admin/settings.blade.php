<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Configurações</h1>

    <form wire:submit="save" class="max-w-md space-y-4 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome da empresa</label>
            <input type="text" wire:model="company_name" placeholder="Dev Quest"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>

            @if ($setting->logo_path && ! $logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" alt="" class="mt-2 h-12 w-auto">
            @endif

            @if ($logo)
                <img src="{{ $logo->temporaryUrl() }}" alt="" class="mt-2 h-12 w-auto">
            @endif

            <input type="file" wire:model="logo" accept="image/*"
                class="mt-2 block w-full text-sm text-gray-700 dark:text-gray-300">
            @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Salvar
        </button>
    </form>
</div>
