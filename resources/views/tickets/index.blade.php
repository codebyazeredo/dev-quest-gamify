<x-layouts.app>
    <div x-data="ticketsTable" x-init="load()">
        <x-page-header title="Chamados" subtitle="Fila de atendimento: priorize pelo prazo, assuma e responda.">
            <a x-bind:href="exportUrl('{{ route('tickets.export') }}')" class="btn btn-outline-secondary bg-body"><i class="bi bi-download" aria-hidden="true"></i> Exportar CSV</a>
        </x-page-header>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <ul class="nav nav-underline" aria-label="Visões da fila">
                @foreach ([
                    'open' => 'Em aberto', 'mine' => 'Meus', 'unassigned' => 'Sem responsável', 'overdue' => 'Prazo vencido',
                    'waiting' => 'Aguardando cliente', 'resolved' => 'Resolvidos', 'all' => 'Todos',
                ] as $view => $label)
                    <li class="nav-item">
                        <button type="button" class="nav-link" x-bind:class="{ active: filters.view === '{{ $view }}' }" x-bind:aria-current="filters.view === '{{ $view }}' ? 'true' : null" x-on:click="setView('{{ $view }}')">
                            {{ $label }}
                            <span class="badge rounded-pill ms-1" x-bind:class="{{ $view === 'overdue' ? "(extra.summary?.overdue > 0 ? 'text-bg-danger' : 'text-bg-secondary')" : ($view === 'unassigned' ? "(extra.summary?.unassigned > 0 ? 'text-bg-warning' : 'text-bg-secondary')" : "'text-bg-secondary'") }}" x-text="extra.summary?.{{ $view }} ?? ''"></span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="small text-body-secondary" title="Tempo médio até a primeira resposta da equipe, nos últimos 7 dias">
                <i class="bi bi-stopwatch" aria-hidden="true"></i>
                1ª resposta (7 dias): <strong class="text-body" x-text="formatMinutes(extra.summary?.avg_first_response_minutes)"></strong>
            </div>
        </div>

        <x-list-filters placeholder="Buscar por assunto, cliente ou nº">
            <select x-model="filters.priority" x-on:change="applyFilters" class="form-select w-auto" aria-label="Prioridade">
                <option value="">Todas as prioridades</option>
                @foreach (\App\Enums\TicketPriority::cases() as $priority)
                    <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                @endforeach
            </select>

            <select x-model="filters.assignee" x-on:change="applyFilters" class="form-select w-auto" aria-label="Responsável">
                <option value="">Todos os responsáveis</option>
                <option value="me">Atribuídos a mim</option>
                <option value="none">Sem responsável</option>
                @foreach ($staff as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>

            <select x-model="filters.status" x-on:change="filters.view = filters.status ? 'all' : 'open'; applyFilters()" class="form-select w-auto" aria-label="Situação">
                <option value="">Qualquer situação</option>
                @foreach (\App\Enums\TicketStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>

            <select x-model="filters.sort" x-on:change="applyFilters" class="form-select w-auto ms-auto" aria-label="Ordenação">
                <option value="due">Ordenar por prazo</option>
                <option value="recent">Mais recentes primeiro</option>
            </select>
        </x-list-filters>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr>
                            <th class="ps-4">Chamado</th>
                            <th>Situação</th>
                            <th>Responsável</th>
                            <th>Prazo de resposta</th>
                            <th>Última atividade</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4" style="min-width: 18rem;">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge rounded-pill mt-1 bg-secondary-subtle text-body-secondary" x-bind:class="`bg-${row.priority_color}-subtle text-${row.priority_color}-emphasis`" x-text="row.priority_label"></span>
                                        <div class="min-w-0">
                                            <a x-bind:href="row.url" class="fw-medium text-body text-decoration-none d-block text-truncate" x-text="`#${row.id} · ${row.title}`"></a>
                                            <div class="small text-body-secondary text-truncate">
                                                <span x-text="row.client"></span>
                                                <span x-show="row.has_task" title="Convertido em tarefa"> · <i class="bi bi-kanban text-primary" aria-hidden="true"></i> tarefa</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="lozenge" x-bind:class="`bg-${row.status_color}-subtle text-${row.status_color}-emphasis`" x-text="row.status_label"></span></td>
                                <td style="min-width: 11rem;">
                                    <template x-if="row.assignee">
                                        <span class="d-inline-flex align-items-center gap-2">
                                            <template x-if="row.assignee.photo_url"><img x-bind:src="row.assignee.photo_url" alt="" class="avatar avatar-sm"></template>
                                            <template x-if="! row.assignee.photo_url"><span class="avatar avatar-sm text-bg-primary" x-text="row.assignee.initials"></span></template>
                                            <span class="text-truncate" x-text="row.assignee.name"></span>
                                        </span>
                                    </template>
                                    <template x-if="! row.assignee">
                                        <button type="button" class="btn btn-outline-primary btn-sm" x-on:click="claim(row)"><i class="bi bi-hand-index-thumb" aria-hidden="true"></i> Assumir</button>
                                    </template>
                                </td>
                                <td class="text-nowrap">
                                    <span x-show="row.sla_label" x-text="row.sla_label"
                                        x-bind:class="{ 'text-danger fw-semibold': row.sla_state === 'overdue', 'text-warning-emphasis fw-medium': row.sla_state === 'soon' }"></span>
                                    <span x-show="! row.sla_label" class="text-body-secondary">—</span>
                                    <div class="small text-body-secondary" x-show="row.due_at" x-text="row.due_at"></div>
                                </td>
                                <td class="small text-body-secondary text-nowrap">
                                    <span x-text="row.last_activity"></span>
                                    <div><i class="bi bi-chat-dots" aria-hidden="true"></i> <span x-text="row.messages_count"></span></div>
                                </td>
                                <td class="text-end pe-4">
                                    <a x-bind:href="row.url" class="btn btn-ghost btn-icon btn-icon-sm" title="Abrir chamado" aria-label="Abrir chamado"><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="6" class="text-center text-body-secondary py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2" aria-hidden="true"></i>
                                Nenhum chamado nesta visão. Ajuste os filtros ou escolha outra visão.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>
    </div>
</x-layouts.app>
