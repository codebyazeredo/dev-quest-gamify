<x-modal title="Editar regra de XP">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-ink">Evento</label>
            <p class="mt-1 text-sm text-ink">{{ $rule->type->label() }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">
                {{ $rule->type->isPercentageBased() ? 'Recompensa (% do valor da tarefa)' : 'Recompensa de XP' }}
            </label>
            <input type="number" min="0" @if ($rule->type->isPercentageBased()) max="100" @endif wire:model="xp_reward" autofocus class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('xp_reward') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" wire:model="active" class="rounded border-line">
            Ativo
        </label>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Salvar alterações
            </button>
        </div>
    </form>
</x-modal>
