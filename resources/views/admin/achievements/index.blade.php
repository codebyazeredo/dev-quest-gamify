<x-layouts.app>
    <div
        x-data="crudTable({
            endpoint: '/api/achievements',
            filters: { search: '', status: '' },
            emptyForm: { name: '', description: '', icon: 'trophy', condition_type: 1, condition_value: 1, xp_reward: 10, active: true, award_title: false, reward_title_name: '', reward_title_icon: 'medal' },
        })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Conquistas" subtitle="Condições, recompensas e títulos das conquistas." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\Achievement::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Nova conquista</x-button>
            @endcan
        </x-page-header>

        <x-list-filters placeholder="Buscar conquistas">
            <select x-model="filters.status" x-on:change="applyFilters" class="form-select w-auto" aria-label="Situação">
                <option value="">Ativas e inativas</option>
                <option value="active">Somente ativas</option>
                <option value="inactive">Somente inativas</option>
            </select>
        </x-list-filters>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Conquista</th>
                            <th>Condição</th>
                            <th>XP</th>
                            <th>Título</th>
                            <th>Desbloqueios</th>
                            <th>Ativa</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <x-icon-chip-js icon="row.icon" color="warning" size="sm" />
                                        <span class="fw-medium" x-text="row.name"></span>
                                    </div>
                                </td>
                                <td x-text="`${row.condition_label} (${row.condition_value})`"></td>
                                <td x-text="row.xp_reward"></td>
                                <td>
                                    <template x-for="title in row.titles" :key="title.name">
                                        <span class="lozenge bg-warning-subtle text-warning-emphasis" x-text="title.name"></span>
                                    </template>
                                    <span x-show="row.titles.length === 0" class="text-body-tertiary">—</span>
                                </td>
                                <td x-text="row.unlocks_count"></td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Ativa" x-bind:checked="row.active" x-on:change="postAction(row, 'toggle-active')">
                                    </div>
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <button type="button" x-on:click="postAction(row, 'duplicate')" title="Duplicar" aria-label="Duplicar" class="btn btn-ghost btn-icon btn-icon-sm"><i class="bi bi-copy" aria-hidden="true"></i></button>
                                    <x-row-actions-js confirm="Excluir esta conquista?" />
                                </td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="7" class="text-center text-body-secondary py-5">Nenhuma conquista encontrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Conquista" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" autofocus />
                    <x-form-icon-picker :icons="$icons" />
                    <x-form-textarea name="description" label="Descrição" rows="2" />

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <x-form-select name="condition_type" label="Condição">
                                @foreach ($conditionTypes as $conditionType)
                                    <option value="{{ $conditionType->value }}">{{ $conditionType->label() }}</option>
                                @endforeach
                            </x-form-select>
                        </div>
                        <div class="col-sm-3"><x-form-input name="condition_value" label="Valor" type="number" min="1" /></div>
                        <div class="col-sm-3"><x-form-input name="xp_reward" label="XP" type="number" min="0" /></div>
                    </div>

                    <div class="rounded-3 border p-3 d-flex align-items-center gap-3 bg-body-tertiary">
                        <x-icon-chip-js icon="form.icon || 'trophy'" color="warning" size="lg" />
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate" x-text="form.name || 'Nome da conquista'"></div>
                            <div class="small text-body-secondary" x-text="form.description || 'Descrição da conquista'"></div>
                            <span class="lozenge bg-secondary-subtle text-secondary-emphasis mt-1" x-text="`+${form.xp_reward} XP`"></span>
                        </div>
                    </div>

                    <x-form-reward-title :icons="$icons" source="conquista" />

                    <template x-if="editingRow"><x-form-switch name="active" label="Ativo" /></template>

                    <x-form-actions create-label="Criar conquista" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
