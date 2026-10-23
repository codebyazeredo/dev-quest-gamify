<div>
    <x-page-header title="Pessoas" subtitle="Dados cadastrais da equipe." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\Person::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Nova pessoa</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$people">
        <x-slot:head>
            <th class="ps-4">Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Usuário</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($people as $person)
            <tr wire:key="person-{{ $person->id }}">
                <td class="ps-4 fw-medium">{{ $person->nome }}</td>
                <td>{{ $person->cpf }}</td>
                <td>{{ $person->email }}</td>
                <td>{{ $person->telefone1 }}</td>
                <td>
                    @if ($person->user()->exists())
                        <x-badge color="success">Vinculado</x-badge>
                    @else
                        <x-badge>Sem usuário</x-badge>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$person->id.')'" :delete="'delete('.$person->id.')'" confirm="Excluir esta pessoa?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-body-secondary py-5">Nenhuma pessoa cadastrada.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.people.create wire:key="person-create" />
    @endif

    @if ($editingPersonId)
        <livewire:admin.people.edit :person-id="$editingPersonId" wire:key="person-edit-{{ $editingPersonId }}" />
    @endif
</div>
