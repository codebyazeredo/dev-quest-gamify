<x-modal title="Editar regra de XP">
    <form wire:submit="save" class="vstack gap-3">
        <div>
            <label class="form-label fw-medium small mb-1">Evento</label>
            <p class="mb-0">{{ $rule->type->label() }}</p>
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

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="active-switch" wire:model="active">
            <label class="form-check-label" for="active-switch">Ativo</label>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
