<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Regras de XP</h1>

        @can('create', \App\Models\TaskEventRule::class)
            @if ($hasUnconfiguredTypes)
                <button type="button" wire:click="toggleCreate" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    + Nova regra
                </button>
            @endif
        @endcan
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Evento</th>
                    <th class="px-4 py-2">Recompensa de XP</th>
                    <th class="px-4 py-2">Ativo</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($rows as $row)
                    @php [$type, $rule] = [$row['type'], $row['rule']]; @endphp
                    <tr wire:key="rule-{{ $type->value }}">
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $type->label() }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                            @if ($rule)
                                {{ $rule->xp_reward }}
                            @else
                                <span class="text-xs text-gray-400">Não configurado</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($rule?->active)
                                <span class="text-green-600">Ativo</span>
                            @elseif ($rule)
                                <span class="text-gray-400">Inativo</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if ($rule)
                                <button type="button" wire:click="edit({{ $type->value }})" title="Editar" aria-label="Editar" class="rounded-md border border-gray-300 p-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"><x-icon name="pencil" class="h-4 w-4" /></button>
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
