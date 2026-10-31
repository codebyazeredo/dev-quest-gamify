<x-layouts.app>
    <div x-data="profilePage" x-on:submit.prevent="save">
        <x-page-header title="Meu perfil" subtitle="Atualize seus dados, sua foto e sua senha." />

        <x-account-tabs />

        <form class="row g-4">
            <div class="col-lg-4">
                <div class="card text-center overflow-hidden">
                    <div class="profile-cover"></div>

                    <div class="card-body pt-0">
                        <div class="profile-photo mb-3">
                            <template x-if="photoPreview()">
                                <img x-bind:src="photoPreview()" alt="" class="avatar avatar-2xl border border-4 border-body">
                            </template>
                            <template x-if="!photoPreview()">
                                <span class="avatar avatar-2xl text-bg-primary border border-4 border-body">{{ auth()->user()->initials() }}</span>
                            </template>
                        </div>

                        <h2 class="h5 mb-1">{{ auth()->user()->name }}</h2>
                        <p class="small text-body-secondary mb-3">{{ auth()->user()->email }}</p>

                        <label for="foto" class="form-label fw-medium small mb-1 d-block text-start">Foto</label>
                        <input type="file" id="foto" accept="image/*" x-on:change="pickFile($event, 'foto')" x-bind:class="{ 'is-invalid': firstError('foto') }" class="form-control">
                        <div class="text-danger small mt-1 text-start" x-text="firstError('foto')"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 vstack gap-4">
                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-person-vcard me-1" aria-hidden="true"></i> Dados pessoais</div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12"><x-form-input name="nome" label="Nome" /></div>
                            <div class="col-sm-6"><x-form-input name="cpf" label="CPF" placeholder="00000000000" /></div>
                            <div class="col-sm-6"><x-form-input name="rg" label="RG" /></div>
                            <div class="col-sm-6"><x-form-input name="nascimento" label="Nascimento" type="date" /></div>
                            <div class="col-sm-6">
                                <x-form-select name="sexo" label="Sexo" placeholder="Selecione">
                                    <template x-for="gender in genders" :key="gender.value">
                                        <option x-bind:value="gender.value" x-text="gender.label"></option>
                                    </template>
                                </x-form-select>
                            </div>
                            <div class="col-sm-6"><x-form-input name="telefone1" label="Telefone 1" /></div>
                            <div class="col-sm-6"><x-form-input name="telefone2" label="Telefone 2" /></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body vstack gap-3">
                        <x-form-address />
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-shield-lock me-1" aria-hidden="true"></i> Senha</div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6"><x-form-input name="password" label="Nova senha" type="password" autocomplete="new-password" placeholder="Deixe em branco para manter" /></div>
                            <div class="col-sm-6"><x-form-input name="password_confirmation" label="Confirmar nova senha" type="password" autocomplete="new-password" /></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <x-button type="submit" x-bind:disabled="isSaving">Salvar alterações</x-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
