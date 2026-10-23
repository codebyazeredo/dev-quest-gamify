<x-modal title="Nova regra de XP">
    <form wire:submit="save" class="vstack gap-3">
        @if (empty($availableTypes))
            <p class="text-body-secondary mb-0">Todos os eventos já possuem uma regra configurada.</p>
        @else
            <x-select name="type" label="Evento" wire:model="type" autofocus placeholder="Selecione...">
                @foreach ($availableTypes as $availableType)
                    <option value="{{ $availableType->value }}">{{ $availableType->label() }}</option>
                @endforeach
            </x-select>

            <div>
                <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />
                <div class="form-text">
                    Para "Aprovado pelo testador" e "Tarefa criada concluída", o valor é uma porcentagem (0-100) do XP da tarefa. Para os demais eventos, é XP fixo.
                </div>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="active-switch" wire:model="active">
                <label class="form-check-label" for="active-switch">Ativo</label>
            </div>
        @endif

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            @if (! empty($availableTypes))
                <x-button type="submit">Criar regra</x-button>
            @endif
        </div>
    </form>
</x-modal>
