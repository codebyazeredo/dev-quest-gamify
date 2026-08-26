<x-modal title="Nova regra de XP">
    <form wire:submit="save" class="space-y-4">
        @if (empty($availableTypes))
            <p class="text-sm text-ink-muted">Todos os eventos já possuem uma regra configurada.</p>
        @else
            <div>
                <label class="block text-sm font-medium text-ink">Evento</label>
                <select wire:model="type" autofocus class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                    <option value="">Selecione...</option>
                    @foreach ($availableTypes as $availableType)
                        <option value="{{ $availableType->value }}">{{ $availableType->label() }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Recompensa de XP</label>
                <input type="number" min="0" wire:model="xp_reward" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                <p class="mt-1 text-xs text-ink-muted">
                    Para "Aprovado pelo testador" e "Tarefa criada concluída", o valor é uma porcentagem (0-100) do XP da tarefa. Para os demais eventos, é XP fixo.
                </p>
                @error('xp_reward') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input type="checkbox" wire:model="active" class="rounded border-line">
                Ativo
            </label>
        @endif

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            @if (! empty($availableTypes))
                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                    Criar regra
                </button>
            @endif
        </div>
    </form>
</x-modal>
