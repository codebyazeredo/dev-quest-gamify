<x-layouts.app>
    <div x-data="challengesTable" x-init="load()" x-on:close-modal="closeModal">
        <x-page-header title="Desafios" subtitle="Metas por período com recompensa em XP e títulos." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\Challenge::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Novo desafio</x-button>
            @endcan
        </x-page-header>

        <x-list-filters placeholder="Buscar desafios">
            <select x-model="filters.status" x-on:change="applyFilters" class="form-select w-auto" aria-label="Situação">
                <option value="">Todas as situações</option>
                <option value="running">Em andamento</option>
                <option value="scheduled">Agendados</option>
                <option value="ended">Encerrados</option>
                <option value="inactive">Inativos</option>
            </select>
        </x-list-filters>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Desafio</th>
                            <th>Meta</th>
                            <th>XP</th>
                            <th>Título</th>
                            <th>Participação</th>
                            <th>Situação</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <x-icon-chip-js icon="row.icon" size="sm" />
                                        <div class="min-w-0">
                                            <div class="fw-medium" x-text="row.name"></div>
                                            <div class="small text-body-secondary" x-text="`${row.type_label} · ${row.period_label}`"></div>
                                        </div>
                                    </div>
                                </td>
                                <td x-text="row.target"></td>
                                <td x-text="row.xp_reward"></td>
                                <td>
                                    <template x-for="title in row.titles" :key="title.name">
                                        <span class="lozenge bg-warning-subtle text-warning-emphasis" x-text="title.name"></span>
                                    </template>
                                    <span x-show="row.titles.length === 0" class="text-body-tertiary">—</span>
                                </td>
                                <td class="small">
                                    <span x-text="`${row.completions_count} / ${row.participants_count}`"></span>
                                    <span class="text-body-secondary">concluíram</span>
                                </td>
                                <td>
                                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Ativo" x-bind:checked="row.active" x-on:change="postAction(row, 'toggle-active')">
                                        <span class="lozenge" x-bind:class="`bg-${row.status_color}-subtle text-${row.status_color}-emphasis`" x-text="row.status_label"></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <button type="button" x-on:click="postAction(row, 'duplicate')" title="Duplicar para o próximo período" aria-label="Duplicar" class="btn btn-ghost btn-icon btn-icon-sm"><i class="bi bi-copy" aria-hidden="true"></i></button>
                                    <x-row-actions-js confirm="Excluir este desafio?" />
                                </td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="7" class="text-center text-body-secondary py-5">Nenhum desafio encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Desafio" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <div class="row g-3">
                        <div class="col-sm-7"><x-form-input name="name" label="Nome" autofocus /></div>
                        <div class="col-sm-5">
                            <x-form-select name="type" label="O que conta como progresso">
                                @foreach ($challengeTypes as $challengeType)
                                    <option value="{{ $challengeType->value }}">{{ $challengeType->label() }}</option>
                                @endforeach
                            </x-form-select>
                        </div>
                    </div>

                    <x-form-textarea name="description" label="Descrição" rows="2" />
                    <x-form-icon-picker :icons="$icons" />

                    <div class="row g-3">
                        <div class="col-sm-6"><x-form-input name="target" label="Meta" type="number" min="1" /></div>
                        <div class="col-sm-6"><x-form-input name="xp_reward" label="Recompensa de XP" type="number" min="0" /></div>
                    </div>

                    <div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                            <span class="form-label fw-medium small mb-0">Período</span>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Atalhos de período">
                                <button type="button" class="btn btn-outline-secondary" x-on:click="applyPeriod('today')">Hoje</button>
                                <button type="button" class="btn btn-outline-secondary" x-on:click="applyPeriod('week')">Esta semana</button>
                                <button type="button" class="btn btn-outline-secondary" x-on:click="applyPeriod('next7')">Próx. 7 dias</button>
                                <button type="button" class="btn btn-outline-secondary" x-on:click="applyPeriod('month')">Este mês</button>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6"><x-form-input name="starts_at" label="Início" type="datetime-local" /></div>
                            <div class="col-sm-6"><x-form-input name="ends_at" label="Fim" type="datetime-local" /></div>
                        </div>
                    </div>

                    <div class="rounded-3 border p-3 d-flex align-items-center gap-3 bg-body-tertiary">
                        <x-icon-chip-js icon="form.icon || 'flag'" size="lg" />
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate" x-text="form.name || 'Nome do desafio'"></div>
                            <div class="small text-body-secondary" x-text="`Meta de ${form.target} · +${form.xp_reward} XP`"></div>
                        </div>
                    </div>

                    <x-form-reward-title :icons="$icons" source="desafio" />

                    <template x-if="editingRow"><x-form-switch name="active" label="Ativo" /></template>

                    <x-form-actions create-label="Criar desafio" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
