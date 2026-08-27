<x-modal title="Nova regra de XP">
    <form wire:submit="save" class="space-y-4">
        @if (empty($availableTypes))
            <p class="text-sm text-ink-muted">Todos os eventos já possuem uma regra configurada.</p>
        @else
            <x-select name="type" label="Evento" wire:model="type" autofocus placeholder="Selecione...">
                @foreach ($availableTypes as $availableType)
                    <option value="{{ $availableType->value }}">{{ $availableType->label() }}</option>
                @endforeach
            </x-select>

            <div>
                <x-input name="xp_reward" label="Recompensa de XP" type="number" min="0" wire:model="xp_reward" />
                <p class="mt-1 text-xs text-ink-muted">
                    Para "Aprovado pelo testador" e "Tarefa criada concluída", o valor é uma porcentagem (0-100) do XP da tarefa. Para os demais eventos, é XP fixo.
                </p>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input type="checkbox" wire:model="active" class="rounded border-line">
                Ativo
            </label>
        @endif

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            @if (! empty($availableTypes))
                <x-button type="submit">Criar regra</x-button>
            @endif
        </div>
    </form>
</x-modal>
