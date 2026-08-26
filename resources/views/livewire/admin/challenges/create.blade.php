<x-modal title="Novo desafio" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-ink">Nome</label>
                <input type="text" wire:model="name" autofocus class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('name') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Tipo</label>
                <select wire:model="type" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                    @foreach ($challengeTypes as $challengeType)
                        <option value="{{ $challengeType->value }}">{{ $challengeType->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Descrição</label>
            <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30"></textarea>
        </div>

        <div class="grid grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-ink">Meta</label>
                <input type="number" min="1" wire:model="target" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Recompensa de XP</label>
                <input type="number" min="0" wire:model="xp_reward" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Início</label>
                <input type="datetime-local" wire:model="starts_at" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Fim</label>
                <input type="datetime-local" wire:model="ends_at" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('ends_at') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Criar desafio
            </button>
        </div>
    </form>
</x-modal>
