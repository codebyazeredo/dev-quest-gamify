<x-modal title="Editar usuário" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <div>
            <span class="form-label fw-medium small d-block mb-1">Nome</span>
            <p class="mb-0">{{ $user->name }}</p>
        </div>

        <x-input name="email" label="E-mail" type="email" wire:model="email" />

        <div class="form-grid form-grid-2">
            <x-input name="password" label="Nova senha (opcional)" type="password" wire:model="password" />

            <x-input name="password_confirmation" label="Confirmar senha" type="password" wire:model="password_confirmation" />
        </div>

        <div>
            <span class="form-label fw-medium small d-block mb-1">Roles</span>
            <div class="d-flex flex-wrap gap-3">
                @foreach ($availableRoles as $roleOption)
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="role-edit-{{ $roleOption }}" value="{{ $roleOption }}" wire:model="roles">
                        <label class="form-check-label" for="role-edit-{{ $roleOption }}">{{ \App\Enums\UserRole::labelFor($roleOption) }}</label>
                    </div>
                @endforeach
            </div>
            @error('roles') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar alterações</x-button>
        </div>
    </form>
</x-modal>
