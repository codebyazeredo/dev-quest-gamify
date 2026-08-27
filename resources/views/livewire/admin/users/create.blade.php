<x-modal title="Novo usuário" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <x-select name="personId" label="Pessoa" wire:model="personId">
            <option value="">Selecione uma pessoa cadastrada</option>
            @forelse ($availablePeople as $person)
                <option value="{{ $person->id }}">{{ $person->nome }} ({{ $person->cpf }})</option>
            @empty
                <option value="" disabled>Nenhuma pessoa disponível — cadastre em "Gerenciar pessoas"</option>
            @endforelse
        </x-select>

        <div class="grid grid-cols-2 gap-3">
            <x-input name="password" label="Senha" type="password" wire:model="password" />

            <x-input name="email" label="E-mail" type="email" wire:model="email" />
        </div>

        <x-input name="password_confirmation" label="Confirmar senha" type="password" wire:model="password_confirmation" />

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
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar usuário</x-button>
        </div>
    </form>
</x-modal>
