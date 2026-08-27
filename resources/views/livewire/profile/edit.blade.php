<x-modal title="Editar meus dados" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input name="nome" label="Nome" wire:model="nome" autofocus />
            </div>

            <x-input name="cpf" label="CPF" wire:model="cpf" placeholder="00000000000" />

            <x-input name="rg" label="RG" wire:model="rg" />

            <x-input name="nascimento" label="Nascimento" type="date" wire:model="nascimento" />

            <x-select name="sexo" label="Sexo" wire:model="sexo" placeholder="Selecione">
                @foreach ($generos as $genero)
                    <option value="{{ $genero->value }}">{{ $genero->label() }}</option>
                @endforeach
            </x-select>

            <x-input name="email" label="E-mail de contato" type="email" wire:model="email" />

            <x-input name="telefone1" label="Telefone 1" wire:model="telefone1" />

            <x-input name="telefone2" label="Telefone 2" wire:model="telefone2" />

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-ink">Foto</label>

                @if ($foto)
                    <img src="{{ $foto->temporaryUrl() }}" alt="" class="mt-2 h-16 w-16 rounded-full object-cover">
                @elseif ($person->foto_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($person->foto_path) }}" alt="" class="mt-2 h-16 w-16 rounded-full object-cover">
                @endif

                <input type="file" wire:model="foto" accept="image/*" class="mt-2 block w-full text-sm text-ink file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/20">
                @error('foto') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="mt-2 border-t border-line pt-4 text-sm font-semibold text-ink">Endereço</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input name="cep" label="CEP" wire:model="cep" placeholder="00000-000" />

            <x-input name="numero" label="Número" wire:model="numero" placeholder="Sem número" />

            <div class="sm:col-span-2">
                <x-input name="logradouro" label="Logradouro" wire:model="logradouro" />
            </div>

            <x-input name="cidade" label="Cidade" wire:model="cidade" />

            <x-input name="estado" label="Estado (UF)" wire:model="estado" maxlength="2" placeholder="SP" class="uppercase" />
        </div>

        <h3 class="mt-2 border-t border-line pt-4 text-sm font-semibold text-ink">Acesso</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input name="login_email" label="E-mail de login" type="email" wire:model="login_email" />

            <div></div>

            <x-input name="password" label="Nova senha" type="password" wire:model="password" placeholder="Deixe em branco para manter" />

            <x-input name="password_confirmation" label="Confirmar nova senha" type="password" wire:model="password_confirmation" />
        </div>

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
