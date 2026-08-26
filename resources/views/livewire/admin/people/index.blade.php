<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Pessoas</h1>

        @can('create', \App\Models\Person::class)
            <button type="button" wire:click="toggleCreate" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Nova pessoa
            </button>
        @endcan
    </div>

    @error('delete') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">CPF</th>
                    <th class="px-4 py-2">E-mail</th>
                    <th class="px-4 py-2">Telefone</th>
                    <th class="px-4 py-2">Usuário</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($people as $person)
                    <tr wire:key="person-{{ $person->id }}">
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $person->nome }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $person->cpf }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $person->email }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $person->telefone1 }}</td>
                        <td class="px-4 py-2">
                            @if ($person->user()->exists())
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700 dark:bg-green-900/40 dark:text-green-300">Vinculado</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">Sem usuário</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button type="button" wire:click="edit({{ $person->id }})"
                                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                Editar
                            </button>
                            <button type="button" wire:click="delete({{ $person->id }})" wire:confirm="Excluir esta pessoa?"
                                class="ml-2 rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Nenhuma pessoa cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $people->links() }}
    </div>

    @if ($showCreateModal)
        <livewire:admin.people.create wire:key="person-create" />
    @endif

    @if ($editingPersonId)
        <livewire:admin.people.edit :person-id="$editingPersonId" wire:key="person-edit-{{ $editingPersonId }}" />
    @endif
</div>
