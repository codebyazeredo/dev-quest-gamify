<x-modal title="Novo usuário" size="lg">
    <form wire:submit="save" class="vstack gap-3">
        <x-select name="personId" label="Pessoa" wire:model="personId">
            <option value="">Selecione uma pessoa cadastrada</option>
            @forelse ($availablePeople as $person)
                <option value="{{ $person->id }}">{{ $person->nome }} ({{ $person->cpf }})</option>
            @empty
                <option value="" disabled>Nenhuma pessoa disponível — cadastre em "Gerenciar pessoas"</option>
            @endforelse
        </x-select>

        <div class="form-grid form-grid-2">
            <x-input name="password" label="Senha" type="password" wire:model="password" />

            <x-input name="email" label="E-mail" type="email" wire:model="email" />
        </div>

        <x-input name="password_confirmation" label="Confirmar senha" type="password" wire:model="password_confirmation" />

        <div>
            <span class="form-label fw-medium small d-block mb-1">Roles</span>
            <div class="d-flex flex-wrap gap-3">
                @foreach ($availableRoles as $roleOption)
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="role-create-{{ $roleOption }}" value="{{ $roleOption }}" wire:model="roles">
                        <label class="form-check-label" for="role-create-{{ $roleOption }}">{{ \App\Enums\UserRole::labelFor($roleOption) }}</label>
                    </div>
                @endforeach
            </div>
            @error('roles') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar usuário</x-button>
        </div>
    </form>
</x-modal>
