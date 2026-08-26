<x-modal title="Novo quadro">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-ink">Nome</label>
            <input id="name" type="text" wire:model="name" required autofocus
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('name') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-ink">Descrição</label>
            <textarea id="description" wire:model="description" rows="3"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30"></textarea>
            @error('description') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="is_active" class="rounded border-line">
            Ativo
        </label>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Criar quadro
            </button>
        </div>
    </form>
</x-modal>
