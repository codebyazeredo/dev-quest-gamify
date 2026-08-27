<x-modal title="Editar título">
    <form wire:submit="save" class="space-y-4">
        <x-input name="name" label="Nome" wire:model="name" autofocus />

        <div>
            <label class="block text-sm font-medium text-ink">Ícone</label>
            <div class="mt-1">
                <x-icon-picker model="icon" :icons="$icons" />
            </div>
            @error('icon') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <x-select name="achievement_id" label="Conquista" wire:model="achievement_id" placeholder="Nenhuma">
            @foreach ($achievements as $achievement)
                <option value="{{ $achievement->id }}">{{ $achievement->name }}</option>
            @endforeach
        </x-select>

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="active" class="rounded border-line">
            Ativo
        </label>

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
