<x-layouts.app>
    <div
        x-data="crudTable({ endpoint: '/api/priorities', emptyForm: { name: '', multiplier: 1 } })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Gravidade" subtitle="Multiplicadores de pontuação por prioridade." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\TaskPriority::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Nova gravidade</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Nome</th>
                            <th>Multiplicador</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4 fw-medium" x-text="row.name"></td>
                                <td><span class="lozenge bg-primary-subtle text-primary-emphasis" x-text="`${row.multiplier}x`"></span></td>
                                <td class="text-end pe-4"><x-row-actions-js confirm="Excluir esta gravidade?" /></td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="3" class="text-center text-body-secondary py-5">Nenhuma gravidade cadastrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Gravidade">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" autofocus />
                    <x-form-input name="multiplier" label="Multiplicador de XP" type="number" min="0" step="0.01" />
                    <x-form-actions create-label="Criar gravidade" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
