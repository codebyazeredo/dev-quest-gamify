<x-modal title="Editar regra de XP">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-ink">Evento</label>
            <p class="mt-1 text-sm text-ink">{{ $rule->type->label() }}</p>
        </div>

        @php
            $xpRewardLabel = $rule->type->isPercentageBased() ? 'Recompensa (% do valor da tarefa)' : 'Recompensa de XP';
        @endphp

        <x-input
            name="xp_reward"
            :label="$xpRewardLabel"
            type="number"
            min="0"
            :max="$rule->type->isPercentageBased() ? 100 : null"
            wire:model="xp_reward"
            autofocus
        />

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
