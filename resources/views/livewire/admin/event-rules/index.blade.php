<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Regras de XP</h1>

        @can('create', \App\Models\TaskEventRule::class)
            @if ($hasUnconfiguredTypes)
                <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                    + Nova regra
                </button>
            @endif
        @endcan
    </div>

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Evento</th>
                    <th class="px-4 py-3">Recompensa de XP</th>
                    <th class="px-4 py-3">Ativo</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @foreach ($rows as $row)
                    @php [$type, $rule] = [$row['type'], $row['rule']]; @endphp
                    <tr wire:key="rule-{{ $type->value }}">
                        <td class="px-4 py-3 text-ink">{{ $type->label() }}</td>
                        <td class="px-4 py-3 text-ink">
                            @if ($rule)
                                {{ $rule->xp_reward }}{{ $type->isPercentageBased() ? '% do valor da tarefa' : ' XP' }}
                            @else
                                <span class="text-xs text-ink-muted">Não configurado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($rule?->active)
                                <x-badge color="forest">Ativo</x-badge>
                            @elseif ($rule)
                                <x-badge>Inativo</x-badge>
                            @else
                                <span class="text-ink-muted">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($rule)
                                <button type="button" wire:click="edit({{ $type->value }})" title="Editar" aria-label="Editar" class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20"><x-icon name="pencil" class="h-4 w-4" /></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($showCreateModal)
        <livewire:admin.event-rules.create wire:key="event-rule-create" />
    @endif

    @if ($editingType)
        <livewire:admin.event-rules.edit :type-value="$editingType" wire:key="event-rule-edit-{{ $editingType }}" />
    @endif
</div>
