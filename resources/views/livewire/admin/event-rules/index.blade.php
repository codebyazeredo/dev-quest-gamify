<div>
    <x-page-header title="Regras de XP" subtitle="Pontuação por evento da tarefa." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\TaskEventRule::class)
            @if ($hasUnconfiguredTypes)
                <x-button wire:click="toggleCreate"><x-icon name="plus" /> Nova regra</x-button>
            @endif
        @endcan
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <th class="ps-4">Evento</th>
            <th>Recompensa de XP</th>
            <th>Status</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @foreach ($rows as $row)
            @php [$type, $rule] = [$row['type'], $row['rule']]; @endphp
            <tr wire:key="rule-{{ $type->value }}">
                <td class="ps-4 fw-medium">{{ $type->label() }}</td>
                <td>
                    @if ($rule)
                        {{ $rule->xp_reward }}{{ $type->isPercentageBased() ? '% do valor da tarefa' : ' XP' }}
                    @else
                        <span class="small text-body-secondary">Não configurado</span>
                    @endif
                </td>
                <td>
                    @if ($rule?->active)
                        <x-badge color="success">Ativo</x-badge>
                    @elseif ($rule)
                        <x-badge>Inativo</x-badge>
                    @else
                        <span class="text-body-secondary">—</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    @if ($rule)
                        <x-row-actions :edit="'edit('.$type->value.')'" />
                    @endif
                </td>
            </tr>
        @endforeach
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.event-rules.create wire:key="event-rule-create" />
    @endif

    @if ($editingType)
        <livewire:admin.event-rules.edit :type-value="$editingType" wire:key="event-rule-edit-{{ $editingType }}" />
    @endif
</div>
