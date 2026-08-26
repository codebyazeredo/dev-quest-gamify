<x-modal title="Nova pessoa" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-ink">Nome</label>
                <input type="text" wire:model="nome" autofocus
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('nome') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">CPF</label>
                <input type="text" wire:model="cpf" placeholder="00000000000"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('cpf') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">RG</label>
                <input type="text" wire:model="rg"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('rg') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Nascimento</label>
                <input type="date" wire:model="nascimento"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('nascimento') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Sexo</label>
                <select wire:model="sexo"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                    <option value="">Selecione</option>
                    @foreach ($generos as $genero)
                        <option value="{{ $genero->value }}">{{ $genero->label() }}</option>
                    @endforeach
                </select>
                @error('sexo') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">E-mail</label>
                <input type="email" wire:model="email"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('email') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Telefone 1</label>
                <input type="text" wire:model="telefone1"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('telefone1') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Telefone 2</label>
                <input type="text" wire:model="telefone2"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('telefone2') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-ink">Foto</label>

                @if ($foto)
                    <img src="{{ $foto->temporaryUrl() }}" alt="" class="mt-2 h-16 w-16 rounded-full object-cover">
                @endif

                <input type="file" wire:model="foto" accept="image/*" class="mt-2 block w-full text-sm text-ink file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/20">
                @error('foto') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="mt-2 text-sm font-semibold text-ink border-t border-line pt-4">Endereço</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-ink">CEP</label>
                <input type="text" wire:model="cep" placeholder="00000-000"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('cep') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Número</label>
                <input type="text" wire:model="numero" placeholder="Sem número"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('numero') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-ink">Logradouro</label>
                <input type="text" wire:model="logradouro"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('logradouro') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Cidade</label>
                <input type="text" wire:model="cidade"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('cidade') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">Estado (UF)</label>
                <input type="text" wire:model="estado" maxlength="2" placeholder="SP"
                    class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink uppercase focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('estado') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Salvar pessoa
            </button>
        </div>
    </form>
</x-modal>
