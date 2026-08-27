<x-modal title="Editar usuário" max-width="max-w-2xl">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-ink">Nome</label>
            <p class="mt-1 text-sm text-ink">{{ $user->name }}</p>
        </div>

        <x-input name="email" label="E-mail" type="email" wire:model="email" />

        <div class="grid grid-cols-2 gap-3">
            <x-input name="password" label="Nova senha (opcional)" type="password" wire:model="password" />

            <x-input name="password_confirmation" label="Confirmar senha" type="password" wire:model="password_confirmation" />
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
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
