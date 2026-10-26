<x-layouts.app>
    <div x-data="rolesTable" x-init="load()" x-on:close-modal="closeModal">
        <x-page-header title="Roles & permissões" subtitle="Marque o que cada papel pode fazer." :back="route('admin.index')" backLabel="Configurações">
            <x-button x-on:click="openCreate"><x-icon name="plus" /> Novo role</x-button>
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Role</th>
                            <template x-for="permission in extra.permissions ?? []" :key="permission.id">
                                <th class="text-center" x-text="permission.label"></th>
                            </template>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4 fw-medium">
                                    <span x-text="row.label"></span>
                                    <span x-show="row.is_protected" class="lozenge bg-warning-subtle text-warning-emphasis ms-1">acesso total</span>
                                </td>
                                <template x-for="permission in extra.permissions ?? []" :key="permission.id">
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center mb-0">
                                            <input type="checkbox" class="form-check-input"
                                                x-bind:aria-label="permission.label"
                                                x-bind:checked="row.is_protected || hasPermission(row, permission)"
                                                x-bind:disabled="row.is_protected"
                                                x-on:change="togglePermission(row, permission)">
                                        </div>
                                    </td>
                                </template>
                                <td class="text-end pe-4">
                                    <template x-if="! row.is_protected"><x-row-actions-js :edit="false" confirm="Excluir este role?" /></template>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td x-bind:colspan="(extra.permissions ?? []).length + 2" class="text-center text-body-secondary py-5">Nenhum role cadastrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Novo role">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <x-form-input name="name" label="Nome" placeholder="ex: financeiro" autofocus />
                    <x-form-actions create-label="Criar role" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
