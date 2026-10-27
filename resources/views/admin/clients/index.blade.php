<x-layouts.app>
    <div
        x-data="clientsTable"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Clientes" subtitle="Empresas com acesso ao portal de chamados." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\Client::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Novo cliente</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Razão social</th>
                            <th>CNPJ</th>
                            <th>E-mail</th>
                            <th>Situação</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4 fw-medium" x-text="row.razao_social"></td>
                                <td x-text="formatCnpj(row.cnpj)"></td>
                                <td x-text="row.email"></td>
                                <td>
                                    <span x-show="row.is_active" class="lozenge bg-success-subtle text-success-emphasis">Ativo</span>
                                    <span x-show="! row.is_active" class="lozenge bg-secondary-subtle text-secondary-emphasis">Inativo</span>
                                </td>
                                <td class="text-end pe-4"><x-row-actions-js confirm="Excluir este cliente?" /></td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="5" class="text-center text-body-secondary py-5">Nenhum cliente cadastrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Cliente">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="razao_social" label="Razão social" autofocus />
                    <x-form-input name="cnpj" label="CNPJ" placeholder="00.000.000/0000-00" inputmode="numeric" x-on:input="form.cnpj = formatCnpj(form.cnpj)" />
                    <x-form-input name="email" label="E-mail de acesso ao portal" type="email" hint="O cliente entra no portal com o CNPJ e este e-mail." />
                    <x-form-switch name="is_active" label="Ativo" />
                    <x-form-actions create-label="Criar cliente" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
