<x-modal title="Editar pessoa" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" wire:model="nome" autofocus
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('nome') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF</label>
                <input type="text" wire:model="cpf" placeholder="00000000000"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('cpf') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">RG</label>
                <input type="text" wire:model="rg"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('rg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nascimento</label>
                <input type="date" wire:model="nascimento"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('nascimento') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexo</label>
                <select wire:model="sexo"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Selecione</option>
                    @foreach ($generos as $genero)
                        <option value="{{ $genero->value }}">{{ $genero->label() }}</option>
                    @endforeach
                </select>
                @error('sexo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                <input type="email" wire:model="email"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone 1</label>
                <input type="text" wire:model="telefone1"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('telefone1') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone 2</label>
                <input type="text" wire:model="telefone2"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('telefone2') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto</label>

                @if ($foto)
                    <img src="{{ $foto->temporaryUrl() }}" alt="" class="mt-2 h-16 w-16 rounded-full object-cover">
                @elseif ($person->foto_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($person->foto_path) }}" alt="" class="mt-2 h-16 w-16 rounded-full object-cover">
                @endif

                <input type="file" wire:model="foto" accept="image/*" class="mt-2 block w-full text-sm text-gray-700 dark:text-gray-300">
                @error('foto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="pt-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Endereço</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                <input type="text" wire:model="cep" placeholder="00000-000"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('cep') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                <input type="text" wire:model="numero" placeholder="Sem número"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('numero') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logradouro</label>
                <input type="text" wire:model="logradouro"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('logradouro') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label>
                <input type="text" wire:model="cidade"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('cidade') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado (UF)</label>
                <input type="text" wire:model="estado" maxlength="2" placeholder="SP"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 uppercase dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('estado') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Salvar alterações
            </button>
        </div>
    </form>
</x-modal>
