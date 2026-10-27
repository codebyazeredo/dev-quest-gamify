<x-layouts.app>
    <div x-data="ticketsTable" x-init="load()">
        <x-page-header title="Chamados" subtitle="Solicitações abertas pelos clientes no portal." />

        <x-list-filters placeholder="Buscar por assunto ou cliente">
            <select x-model="filters.status" x-on:change="applyFilters" class="form-select w-auto" aria-label="Situação">
                <option value="">Todas as situações</option>
                @foreach (\App\Enums\TicketStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </x-list-filters>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">#</th>
                            <th>Assunto</th>
                            <th>Cliente</th>
                            <th>Situação</th>
                            <th>Aberto em</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4 text-body-secondary" x-text="`#${row.id}`"></td>
                                <td class="fw-medium"><a x-bind:href="row.url" class="text-body text-decoration-none" x-text="row.title"></a></td>
                                <td x-text="row.client"></td>
                                <td><span class="lozenge" x-bind:class="`bg-${row.status_color}-subtle text-${row.status_color}-emphasis`" x-text="row.status_label"></span></td>
                                <td x-text="row.created_at"></td>
                                <td class="text-end pe-4">
                                    <span x-show="row.has_task" class="me-2" title="Convertido em tarefa"><i class="bi bi-kanban text-primary" aria-hidden="true"></i></span>
                                    <a x-bind:href="row.url" class="btn btn-ghost btn-icon btn-icon-sm" title="Abrir chamado" aria-label="Abrir chamado"><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="6" class="text-center text-body-secondary py-5">Nenhum chamado encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>
    </div>
</x-layouts.app>
