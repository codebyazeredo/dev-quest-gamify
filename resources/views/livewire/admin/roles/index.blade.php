<div>
    <x-page-header title="Roles & permissões" :back="route('admin.index')" backLabel="Configurações">
        <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
            + Novo role
        </button>
    </x-page-header>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Role</th>
                    @foreach ($permissions as $permission)
                        <th class="px-3 py-3 text-center">{{ \App\Support\PermissionLabel::for($permission->name) }}</th>
                    @endforeach
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($roles as $role)
                    <tr wire:key="role-{{ $role->id }}">
                        <td class="px-4 py-3 font-medium text-ink">
                            {{ \App\Enums\UserRole::labelFor($role->name) }}
                            @if ($role->name === 'admin')
                                <span class="ml-1 text-xs text-ink-muted">(acesso total)</span>
                            @endif
                        </td>
                        @foreach ($permissions as $permission)
                            <td class="px-3 py-3 text-center">
                                <input type="checkbox"
                                    @checked($role->hasPermissionTo($permission))
                                    @disabled($role->name === 'admin')
                                    wire:click="togglePermission({{ $role->id }}, {{ $permission->id }})"
                                    class="rounded border-line">
                            </td>
                        @endforeach
                        <td class="px-4 py-3 text-right">
                            @unless ($role->name === 'admin')
                                <button type="button" wire:click="delete({{ $role->id }})" wire:confirm="Excluir este role?" title="Excluir" aria-label="Excluir"
                                    class="rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $permissions->count() + 2 }}" class="px-4 py-6 text-center text-ink-muted">Nenhum role cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-per-page-selector />
        {{ $roles->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.roles.create wire:key="role-create" />
    @endif
</div>
