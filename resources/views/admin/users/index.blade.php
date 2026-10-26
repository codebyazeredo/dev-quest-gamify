<x-layouts.app>
    <div
        x-data="crudTable({ endpoint: '/api/users', emptyForm: { person_id: '', email: '', password: '', password_confirmation: '', roles: [] } })"
        x-init="load()"
        x-on:close-modal="closeModal"
    >
        <x-page-header title="Usuários" subtitle="Contas de acesso ao sistema." :back="route('admin.index')" backLabel="Configurações">
            <x-icon-button :href="route('admin.people')" icon="person-vcard" label="Gerenciar pessoas" />

            @can('create', \App\Models\User::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Novo usuário</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Nome</th>
                            <th>E-mail</th>
                            <th>Roles</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <template x-if="row.photo_url"><img x-bind:src="row.photo_url" alt="" class="avatar avatar-sm"></template>
                                        <template x-if="! row.photo_url"><span class="avatar avatar-sm text-bg-primary" x-text="row.initials"></span></template>
                                        <span class="fw-medium" x-text="row.name"></span>
                                    </div>
                                </td>
                                <td x-text="row.email"></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <template x-for="label in row.role_labels" :key="label">
                                            <span class="lozenge bg-primary-subtle text-primary-emphasis" x-text="label"></span>
                                        </template>
                                        <span x-show="row.role_labels.length === 0" class="small text-body-secondary">Sem role</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4"><x-row-actions-js confirm="Excluir este usuário?" /></td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="4" class="text-center text-body-secondary py-5">Nenhum usuário cadastrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Usuário" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <template x-if="! editingRow">
                        <x-form-select name="person_id" label="Pessoa" placeholder="Selecione uma pessoa cadastrada">
                            <template x-for="person in extra.form_options?.people ?? []" :key="person.id">
                                <option x-bind:value="person.id" x-text="person.label"></option>
                            </template>
                        </x-form-select>
                    </template>

                    <template x-if="editingRow">
                        <div>
                            <span class="form-label fw-medium small d-block mb-1">Nome</span>
                            <p class="mb-0" x-text="editingRow.name"></p>
                        </div>
                    </template>

                    <x-form-input name="email" label="E-mail" type="email" />

                    <div class="form-grid form-grid-2">
                        <x-form-input name="password" label="Senha" type="password" autocomplete="new-password" hint="Na edição, deixe em branco para manter a senha atual." />
                        <x-form-input name="password_confirmation" label="Confirmar senha" type="password" autocomplete="new-password" />
                    </div>

                    <div>
                        <span class="form-label fw-medium small d-block mb-1">Roles</span>
                        <div class="d-flex flex-wrap gap-3">
                            <template x-for="role in extra.form_options?.roles ?? []" :key="role.name">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" x-bind:id="`role-${role.name}`" x-bind:value="role.name" x-model="form.roles">
                                    <label class="form-check-label" x-bind:for="`role-${role.name}`" x-text="role.label"></label>
                                </div>
                            </template>
                        </div>
                        <div class="text-danger small mt-1" x-text="firstError('roles')"></div>
                    </div>

                    <x-form-actions create-label="Criar usuário" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
