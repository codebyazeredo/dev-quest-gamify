<div>
    <x-page-header title="Gravidade" subtitle="Multiplicadores de pontuação por prioridade." :back="route('admin.index')" backLabel="Configurações">
        @can('create', \App\Models\TaskPriority::class)
            <x-button wire:click="toggleCreate"><x-icon name="plus" /> Nova gravidade</x-button>
        @endcan
    </x-page-header>

    @error('delete') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror

    <x-data-table :paginator="$priorities">
        <x-slot:head>
            <th class="ps-4">Nome</th>
            <th>Multiplicador</th>
            <th class="text-end pe-4"></th>
        </x-slot:head>

        @forelse ($priorities as $priority)
            <tr wire:key="priority-{{ $priority->id }}">
                <td class="ps-4 fw-medium">{{ $priority->name }}</td>
                <td><x-badge color="primary">{{ $priority->multiplier }}x</x-badge></td>
                <td class="text-end pe-4">
                    <x-row-actions :edit="'edit('.$priority->id.')'" :delete="'delete('.$priority->id.')'" confirm="Excluir esta gravidade?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-body-secondary py-5">Nenhuma gravidade cadastrada.</td>
            </tr>
        @endforelse
    </x-data-table>

    @if ($showCreateModal)
        <livewire:admin.priorities.create wire:key="priority-create" />
    @endif

    @if ($editingPriorityId)
        <livewire:admin.priorities.edit :priority-id="$editingPriorityId" wire:key="priority-edit-{{ $editingPriorityId }}" />
    @endif
</div>
