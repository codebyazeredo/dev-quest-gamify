<x-layouts.app>
    <div
        x-data="crudTable({ endpoint: '/api/event-rules', keyField: 'type', emptyForm: { type: '', xp_reward: 0, active: true } })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Regras de XP" subtitle="Pontuação por evento da tarefa." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\TaskEventRule::class)
                <x-button x-show="rows.some((row) => ! row.configured)" x-cloak x-on:click="openCreate"><x-icon name="plus" /> Nova regra</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Evento</th>
                            <th>Recompensa de XP</th>
                            <th>Status</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.type">
                            <tr>
                                <td class="ps-4 fw-medium" x-text="row.label"></td>
                                <td>
                                    <span x-show="row.configured" x-text="`${row.xp_reward}${row.is_percentage ? '% do valor da tarefa' : ' XP'}`"></span>
                                    <span x-show="! row.configured" class="small text-body-secondary">Não configurado</span>
                                </td>
                                <td>
                                    <span x-show="row.configured && row.active" class="lozenge bg-success-subtle text-success-emphasis">Ativo</span>
                                    <span x-show="row.configured && ! row.active" class="lozenge bg-secondary-subtle text-secondary-emphasis">Inativo</span>
                                    <span x-show="! row.configured" class="text-body-secondary">—</span>
                                </td>
                                <td class="text-end pe-4">
                                    <template x-if="row.configured"><x-row-actions-js :delete="false" /></template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Regra de XP">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <template x-if="editingRow">
                        <div>
                            <label class="form-label fw-medium small mb-1">Evento</label>
                            <p class="mb-0" x-text="editingRow.label"></p>
                        </div>
                    </template>

                    <template x-if="! editingRow">
                        <x-form-select name="type" label="Evento" placeholder="Selecione...">
                            <template x-for="row in rows.filter((row) => ! row.configured)" :key="row.type">
                                <option x-bind:value="row.type" x-text="row.label"></option>
                            </template>
                        </x-form-select>
                    </template>

                    <x-form-input name="xp_reward" label="Recompensa de XP" type="number" min="0" hint="Para eventos percentuais ('Aprovado pelo testador', 'Tarefa criada concluída') o valor é uma porcentagem (0-100) do XP da tarefa. Nos demais, é XP fixo." />
                    <x-form-switch name="active" label="Ativo" />
                    <x-form-actions create-label="Criar regra" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
