<x-layouts.app>
    <div
        x-data="crudTable({ endpoint: '/api/business-sectors', emptyForm: { name: '' } })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Ramos de atuação" subtitle="Segmentos de mercado dos clientes." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\BusinessSector::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Novo ramo</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Nome</th>
                            <th>Clientes</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4 fw-medium" x-text="row.name"></td>
                                <td><span class="lozenge bg-primary-subtle text-primary-emphasis" x-text="row.clients_count"></span></td>
                                <td class="text-end pe-4"><x-row-actions-js confirm="Excluir este ramo de atuação?" /></td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="3" class="text-center text-body-secondary py-5">Nenhum ramo cadastrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Ramo de atuação">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" autofocus />
                    <x-form-actions create-label="Criar ramo" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
