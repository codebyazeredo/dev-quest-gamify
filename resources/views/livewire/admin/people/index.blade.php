<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Pessoas</h1>

        @can('create', \App\Models\Person::class)
            <button type="button" wire:click="toggleCreate" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
                + Nova pessoa
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-terracotta">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">CPF</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Telefone</th>
                    <th class="px-4 py-3">Usuário</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($people as $person)
                    <tr wire:key="person-{{ $person->id }}">
                        <td class="px-4 py-3 text-ink">{{ $person->nome }}</td>
                        <td class="px-4 py-3 text-ink">{{ $person->cpf }}</td>
                        <td class="px-4 py-3 text-ink">{{ $person->email }}</td>
                        <td class="px-4 py-3 text-ink">{{ $person->telefone1 }}</td>
                        <td class="px-4 py-3">
                            @if ($person->user()->exists())
                                <x-badge color="forest">Vinculado</x-badge>
                            @else
                                <x-badge>Sem usuário</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="edit({{ $person->id }})" title="Editar" aria-label="Editar"
                                class="rounded-lg border border-line p-1.5 text-ink-muted hover:bg-line/20">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <button type="button" wire:click="delete({{ $person->id }})" wire:confirm="Excluir esta pessoa?" title="Excluir" aria-label="Excluir"
                                class="ml-2 rounded-lg border border-terracotta/30 p-1.5 text-terracotta hover:bg-terracotta/10">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-ink-muted">Nenhuma pessoa cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-per-page-selector />
        {{ $people->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.people.create wire:key="person-create" />
    @endif

    @if ($editingPersonId)
        <livewire:admin.people.edit :person-id="$editingPersonId" wire:key="person-edit-{{ $editingPersonId }}" />
    @endif
</div>
