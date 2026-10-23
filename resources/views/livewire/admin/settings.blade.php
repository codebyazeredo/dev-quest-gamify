<div>
    <x-page-header title="Identidade da empresa" subtitle="Nome, logo e cor principal exibidos para todos os usuários." :back="route('admin.index')" backLabel="Configurações" />

    <form wire:submit="save">
        <div class="row g-4">
            <div class="col-lg-6">
                <x-card padding="p-4" class="h-100">
                    <h2 class="h5 mb-3"><i class="bi bi-building me-2" aria-hidden="true"></i>Marca</h2>

                    <div class="vstack gap-4">
                        <x-input name="company_name" label="Nome da empresa" wire:model="company_name" placeholder="{{ \App\Models\AppSetting::DEFAULT_NAME }}" />

                        <div>
                            <label for="logo" class="form-label fw-medium small mb-1">Logo</label>

                            @if ($logo || $setting->logoUrl())
                                <div class="d-flex align-items-center justify-content-center rounded-3 border bg-body-tertiary p-4 mb-2">
                                    <img src="{{ $logo ? $logo->temporaryUrl() : $setting->logoUrl() }}" alt="" class="img-fluid" style="max-height: 9rem;">
                                </div>
                            @endif

                            <input type="file" id="logo" wire:model="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <span class="form-label fw-medium small d-block mb-1">Tamanho da logo na tela de login</span>

                            <div class="btn-group w-100" role="group" aria-label="Tamanho da logo">
                                @foreach (\App\Enums\LogoSize::cases() as $size)
                                    <input type="radio" class="btn-check" name="logo_size" id="logo-size-{{ $size->value }}" wire:model.live="logo_size" value="{{ $size->value }}" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="logo-size-{{ $size->value }}">{{ $size->label() }}</label>
                                @endforeach
                            </div>
                            @error('logo_size') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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
                                wire:click="$set('primary_color', '{{ $presetColor }}')"
                                title="{{ $presetName }}"
                                aria-label="{{ $presetName }}"
                                aria-pressed="{{ strtolower($primary_color) === $presetColor ? 'true' : 'false' }}"
                                class="btn btn-icon rounded-circle border-2 {{ strtolower($primary_color) === $presetColor ? 'border-dark' : 'border-white' }} shadow-sm"
                                style="background-color: {{ $presetColor }}; width: 2.25rem; height: 2.25rem;"
                            >
                                @if (strtolower($primary_color) === $presetColor)
                                    <i class="bi bi-check-lg text-white" aria-hidden="true"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <label for="primary_color" class="form-label fw-medium small mb-1">Ou escolha qualquer cor</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" id="primary_color" wire:model.live.debounce.300ms="primary_color" class="form-control form-control-color @error('primary_color') is-invalid @enderror" title="Escolher cor primária">
                        <code class="text-body-secondary">{{ strtolower($primary_color) }}</code>
                    </div>
                    @error('primary_color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                    <div class="theme-preview border rounded-3 p-3 mt-4 bg-body-tertiary">
                        <x-theme-style :color="$primary_color" selector=".theme-preview" />

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
            <x-button type="submit"><i class="bi bi-check2-circle" aria-hidden="true"></i> Salvar configurações</x-button>
        </div>
    </form>
</div>
