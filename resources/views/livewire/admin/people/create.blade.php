<x-modal title="Nova pessoa" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <div class="row g-3">
            <div class="col-12">
                <x-input name="nome" label="Nome" wire:model="nome" autofocus />
            </div>

            <div class="col-sm-6">
                <x-input name="cpf" label="CPF" wire:model="cpf" placeholder="00000000000" />
            </div>

            <div class="col-sm-6">
                <x-input name="rg" label="RG" wire:model="rg" />
            </div>

            <div class="col-sm-6">
                <x-input name="nascimento" label="Nascimento" type="date" wire:model="nascimento" />
            </div>

            <div class="col-sm-6">
                <x-select name="sexo" label="Sexo" wire:model="sexo" placeholder="Selecione">
                    @foreach ($generos as $genero)
                        <option value="{{ $genero->value }}">{{ $genero->label() }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="col-sm-6">
                <x-input name="email" label="E-mail" type="email" wire:model="email" />
            </div>

            <div class="col-sm-6">
                <x-input name="telefone1" label="Telefone 1" wire:model="telefone1" />
            </div>

            <div class="col-sm-6">
                <x-input name="telefone2" label="Telefone 2" wire:model="telefone2" />
            </div>

            <div class="col-12">
                <label for="person-foto" class="form-label fw-medium small mb-1">Foto</label>

                <div class="d-flex align-items-center gap-3">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" alt="" class="avatar avatar-lg">
                    @endif

                    <input type="file" id="person-foto" wire:model="foto" accept="image/*" class="form-control @error('foto') is-invalid @enderror">
                </div>
                @error('foto') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <h3 class="h6 border-top pt-3 mb-0">Endereço</h3>

        <div class="row g-3">
            <div class="col-sm-6">
                <x-input name="cep" label="CEP" wire:model="cep" placeholder="00000-000" />
            </div>

            <div class="col-sm-6">
                <x-input name="numero" label="Número" wire:model="numero" placeholder="Sem número" />
            </div>

            <div class="col-12">
                <x-input name="logradouro" label="Logradouro" wire:model="logradouro" />
            </div>

            <div class="col-sm-6">
                <x-input name="cidade" label="Cidade" wire:model="cidade" />
            </div>

            <div class="col-sm-6">
                <x-input name="estado" label="Estado (UF)" wire:model="estado" maxlength="2" placeholder="SP" class="text-uppercase" />
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar pessoa</x-button>
        </div>
    </form>
</x-modal>
