<x-modal title="Novo usuário" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-ink">Pessoa</label>
            <select wire:model="personId" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                <option value="">Selecione uma pessoa cadastrada</option>
                @forelse ($availablePeople as $person)
                    <option value="{{ $person->id }}">{{ $person->nome }} ({{ $person->cpf }})</option>
                @empty
                    <option value="" disabled>Nenhuma pessoa disponível — cadastre em "Gerenciar pessoas"</option>
                @endforelse
            </select>
            @error('personId') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-ink">Senha</label>
                <input type="password" wire:model="password" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('password') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink">E-mail</label>
                <input type="email" wire:model="email" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                @error('email') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Confirmar senha</label>
            <input type="password" wire:model="password_confirmation" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <div>
            <label class="block text-sm font-medium text-ink">Roles</label>
            <div class="mt-1 flex flex-wrap gap-2">
                @foreach ($availableRoles as $roleOption)
                    <label class="flex items-center gap-1 rounded-lg border border-line px-2 py-1 text-xs">
                        <input type="checkbox" value="{{ $roleOption }}" wire:model="roles" class="rounded border-line">
                        {{ \App\Enums\UserRole::labelFor($roleOption) }}
                    </label>
                @endforeach
            </div>
            @error('roles') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Criar usuário
            </button>
        </div>
    </form>
</x-modal>
