<x-layouts.app>
    <div x-data="settingsForm(@js($setting))">
        <x-page-header title="Identidade da empresa" subtitle="Nome, logo e cor principal exibidos para todos os usuários." :back="route('admin.index')" backLabel="Configurações" />

        <form x-on:submit.prevent="save">
            <div class="row g-4">
                <div class="col-lg-6">
                    <x-card padding="p-4" class="h-100">
                        <h2 class="h5 mb-3"><i class="bi bi-building me-2" aria-hidden="true"></i>Marca</h2>

                        <div class="vstack gap-4">
                            <div>
                                <label for="company_name" class="form-label fw-medium small mb-1">Nome da empresa</label>
                                <input id="company_name" type="text" x-model="form.company_name" x-bind:class="{ 'is-invalid': firstError('company_name') }" class="form-control" placeholder="{{ \App\Models\AppSetting::DEFAULT_NAME }}">
                                <div class="invalid-feedback" x-text="firstError('company_name')"></div>
                            </div>

                            <div>
                                <label for="logo" class="form-label fw-medium small mb-1">Logo</label>

                                <template x-if="logoPreview()">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 border bg-body-tertiary p-4 mb-2">
                                        <img x-bind:src="logoPreview()" alt="" class="img-fluid" style="max-height: 9rem;">
                                    </div>
                                </template>

                                <input type="file" id="logo" accept="image/*" x-on:change="pickLogo" x-bind:class="{ 'is-invalid': firstError('logo') }" class="form-control">
                                <div class="invalid-feedback" x-text="firstError('logo')"></div>
                            </div>

                            <div>
                                <span class="form-label fw-medium small d-block mb-1">Tamanho da logo na tela de login</span>

                                <div class="btn-group w-100" role="group" aria-label="Tamanho da logo">
                                    @foreach ($logoSizes as $size)
                                        <input type="radio" class="btn-check" name="logo_size" id="logo-size-{{ $size->value }}" x-model="form.logo_size" value="{{ $size->value }}" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="logo-size-{{ $size->value }}">{{ $size->label() }}</label>
                                    @endforeach
                                </div>
                                <div class="text-danger small mt-1" x-text="firstError('logo_size')"></div>
                            </div>
                        </div>
                    </x-card>
                </div>

                <div class="col-lg-6">
                    <x-card padding="p-4" class="h-100">
                        <h2 class="h5 mb-1"><i class="bi bi-palette me-2" aria-hidden="true"></i>Cor primária</h2>
                        <p class="small text-body-secondary mb-3">Aplicada em botões, menu, links e destaques de todo o sistema.</p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($colorPresets as $presetName => $presetColor)
                                <button
                                    type="button"
                                    x-on:click="form.primary_color = '{{ $presetColor }}'"
                                    title="{{ $presetName }}"
                                    aria-label="{{ $presetName }}"
                                    x-bind:aria-pressed="form.primary_color.toLowerCase() === '{{ $presetColor }}'"
                                    x-bind:class="form.primary_color.toLowerCase() === '{{ $presetColor }}' ? 'border-dark' : 'border-white'"
                                    class="btn btn-icon rounded-circle border-2 shadow-sm"
                                    style="background-color: {{ $presetColor }}; width: 2.25rem; height: 2.25rem;"
                                >
                                    <i x-show="form.primary_color.toLowerCase() === '{{ $presetColor }}'" x-cloak class="bi bi-check-lg text-white" aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>

                        <label for="primary_color" class="form-label fw-medium small mb-1">Ou escolha qualquer cor</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" id="primary_color" x-model="form.primary_color" x-bind:class="{ 'is-invalid': firstError('primary_color') }" class="form-control form-control-color" title="Escolher cor primária">
                            <code class="text-body-secondary" x-text="form.primary_color.toLowerCase()"></code>
                        </div>
                        <div class="text-danger small mt-1" x-text="firstError('primary_color')"></div>

                        <div class="theme-preview border rounded-3 p-3 mt-4 bg-body-tertiary" x-bind:style="themePreviewStyle()">
                            <p class="small text-body-secondary text-uppercase fw-semibold mb-3">Pré-visualização</p>

                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                <button type="button" class="btn btn-primary">Botão primário</button>
                                <button type="button" class="btn btn-outline-primary">Contorno</button>
                                <span class="badge text-bg-primary">Destaque</span>
                                <a href="#" class="link-primary" onclick="return false;">Link</a>
                            </div>

                            <div class="progress mb-3" role="progressbar" aria-label="Exemplo de progresso" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: 65%"></div>
                            </div>

                            <div class="alert alert-primary mb-0 py-2 small" role="alert">Assim os avisos e destaques vão aparecer.</div>
                        </div>
                    </x-card>
                </div>
            </div>

            <div class="mt-4">
                <x-button type="submit" x-bind:disabled="isSaving"><i class="bi bi-check2-circle" aria-hidden="true"></i> Salvar configurações</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
