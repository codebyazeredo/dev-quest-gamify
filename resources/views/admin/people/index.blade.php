<x-layouts.app>
    <div x-data="peopleTable" x-init="load()" x-on:close-modal="closeModal">
        <x-page-header title="Pessoas" subtitle="Dados cadastrais da equipe." :back="route('admin.index')" backLabel="Configurações">
            @can('create', \App\Models\Person::class)
                <x-button x-on:click="openCreate"><x-icon name="plus" /> Nova pessoa</x-button>
            @endcan
        </x-page-header>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="app-thead">
                        <tr class="small text-body-secondary text-uppercase">
                            <th class="ps-4">Nome</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Usuário</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="ps-4 fw-medium" x-text="row.nome"></td>
                                <td x-text="row.cpf"></td>
                                <td x-text="row.email"></td>
                                <td x-text="row.telefone1"></td>
                                <td>
                                    <span x-show="row.has_user" class="lozenge bg-success-subtle text-success-emphasis">Vinculado</span>
                                    <span x-show="! row.has_user" class="lozenge bg-secondary-subtle text-secondary-emphasis">Sem usuário</span>
                                </td>
                                <td class="text-end pe-4"><x-row-actions-js confirm="Excluir esta pessoa?" /></td>
                            </tr>
                        </template>

                        <tr x-show="! isLoading && rows.length === 0" x-cloak>
                            <td colspan="6" class="text-center text-body-secondary py-5">Nenhuma pessoa cadastrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-crud-pager />
        </div>

        <template x-if="isModalOpen">
            <x-modal title="Pessoa" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <div class="row g-3">
                        <div class="col-12"><x-form-input name="nome" label="Nome" autofocus /></div>
                        <div class="col-sm-6"><x-form-input name="cpf" label="CPF" placeholder="00000000000" /></div>
                        <div class="col-sm-6"><x-form-input name="rg" label="RG" /></div>
                        <div class="col-sm-6"><x-form-input name="nascimento" label="Nascimento" type="date" /></div>
                        <div class="col-sm-6">
                            <x-form-select name="sexo" label="Sexo" placeholder="Selecione">
                                <template x-for="gender in extra.genders ?? []" :key="gender.value">
                                    <option x-bind:value="gender.value" x-text="gender.label"></option>
                                </template>
                            </x-form-select>
                        </div>
                        <div class="col-sm-6"><x-form-input name="email" label="E-mail" type="email" /></div>
                        <div class="col-sm-6"><x-form-input name="telefone1" label="Telefone 1" /></div>
                        <div class="col-sm-6"><x-form-input name="telefone2" label="Telefone 2" /></div>

                        <div class="col-12">
                            <label for="foto" class="form-label fw-medium small mb-1">Foto</label>

                            <div class="d-flex align-items-center gap-3">
                                <template x-if="photoPreview()"><img x-bind:src="photoPreview()" alt="" class="avatar avatar-lg"></template>
                                <input type="file" id="foto" accept="image/*" x-on:change="pickPhoto" x-bind:class="{ 'is-invalid': firstError('foto') }" class="form-control">
                            </div>
                            <div class="text-danger small mt-1" x-text="firstError('foto')"></div>
                        </div>
                    </div>

                    <h3 class="h6 border-top pt-3 mb-0">Endereço</h3>

                    <div class="row g-3">
                        <div class="col-sm-6"><x-form-input name="cep" label="CEP" placeholder="00000-000" /></div>
                        <div class="col-sm-6"><x-form-input name="numero" label="Número" placeholder="Sem número" /></div>
                        <div class="col-12"><x-form-input name="logradouro" label="Logradouro" /></div>
                        <div class="col-sm-6"><x-form-input name="cidade" label="Cidade" /></div>
                        <div class="col-sm-6"><x-form-input name="estado" label="Estado (UF)" maxlength="2" placeholder="SP" class="text-uppercase" /></div>
                    </div>

                    <x-form-actions create-label="Salvar pessoa" />
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
