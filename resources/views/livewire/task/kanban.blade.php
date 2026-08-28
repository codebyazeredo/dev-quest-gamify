<div class="flex flex-1 min-h-0 flex-col">
    <div x-data="{ draggingTaskId: null }" class="flex flex-1 min-h-0 gap-4 overflow-x-auto pb-4">
        @foreach ($board->columns as $column)
            <div
                class="flex w-64 flex-shrink-0 flex-col rounded-xl border border-line bg-line/20 p-3"
                x-on:dragover.prevent
                x-on:drop="$wire.moveTask(draggingTaskId, {{ $column->id }}, {{ $column->tasks->count() }})"
                wire:key="column-{{ $column->id }}"
            >
                <div class="mb-3 flex shrink-0 items-start justify-between gap-2 border-b border-line pb-2">
                    <div class="min-w-0 flex-1">
                        <input
                            type="text"
                            value="{{ $column->name }}"
                            wire:change="renameColumn({{ $column->id }}, $event.target.value)"
                            @can('update', $board) @else disabled @endcan
                            class="w-full min-w-0 truncate rounded bg-transparent px-1 text-sm font-semibold text-ink focus:bg-card focus:outline-none focus:ring-1 focus:ring-primary/40"
                        >
                        @if ($column->status)
                            <span class="mt-1 inline-flex items-center rounded-full bg-primary/10 px-1.5 py-0.5 text-[10px] font-medium text-primary">
                                {{ $column->status->label() }}
                            </span>
                        @endif
                    </div>

                    <span class="shrink-0 rounded-full bg-card px-1.5 py-0.5 text-xs text-ink-muted">{{ $column->tasks->count() }}</span>

                    @can('update', $board)
                        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open" title="Configurações da coluna" aria-label="Configurações da coluna" class="rounded p-1 text-ink-muted hover:bg-line/30">
                                <x-icon name="gear" class="h-4 w-4" />
                            </button>

                            <div x-show="open" x-cloak x-transition class="absolute right-0 z-10 mt-1 w-56 space-y-3 rounded-xl border border-line bg-card p-3 text-sm shadow-lg">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-ink-muted">Marco de pontuação</label>
                                    <select
                                        wire:change="setMilestone({{ $column->id }}, $event.target.value === '' ? null : $event.target.value)"
                                        class="w-full rounded-lg border border-line bg-card px-2 py-1 text-sm text-ink"
                                    >
                                        <option value="" @selected(! $column->status)>Nenhum (livre)</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}" @selected($column->status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex items-center justify-between gap-1">
                                    <button type="button" @click="open = false" wire:click="moveColumnUp({{ $column->id }})" class="flex-1 rounded px-2 py-1 text-ink-muted hover:bg-line/20">&larr; Mover</button>
                                    <button type="button" @click="open = false" wire:click="moveColumnDown({{ $column->id }})" class="flex-1 rounded px-2 py-1 text-ink-muted hover:bg-line/20">Mover &rarr;</button>
                                </div>

                                <button type="button" @click="open = false" wire:click="confirmDeleteColumn({{ $column->id }})" class="w-full rounded px-2 py-1 text-left text-terracotta hover:bg-terracotta/10">
                                    Excluir lista
                                </button>
                            </div>
                        </div>
                    @endcan
                </div>

                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto">
                    @foreach ($column->tasks as $task)
                        <x-task-card :task="$task" wire:key="task-{{ $task->id }}" />
                    @endforeach
                </div>

                @can('create', \App\Models\Task::class)
                    <button
                        type="button"
                        wire:click="openCreate({{ $column->id }})"
                        class="mt-2 shrink-0 rounded-lg px-2 py-1.5 text-left text-sm text-ink-muted hover:bg-line/30"
                    >
                        + Adicionar tarefa
                    </button>
                @endcan
            </div>
        @endforeach

        @can('update', $board)
            <div class="w-64 flex-shrink-0 rounded-xl border border-dashed border-line p-3">
                <form wire:submit="addColumn" class="flex items-center gap-2">
                    <input
                        type="text"
                        wire:model="newColumnName"
                        placeholder="+ Adicionar coluna"
                        class="min-w-0 flex-1 rounded-lg border border-line bg-card px-2 py-1.5 text-sm text-ink"
                    >
                    <button type="submit" class="shrink-0 rounded-lg bg-primary px-2 py-1.5 text-sm font-semibold text-white hover:bg-primary-hover">
                        Adicionar
                    </button>
                </form>
                @error('newColumnName') <p class="mt-1 text-xs text-terracotta">{{ $message }}</p> @enderror
            </div>
        @endcan
    </div>

    @if ($creatingInColumnId)
        <livewire:task.create :board="$board" :column-id="$creatingInColumnId" wire:key="task-create-{{ $creatingInColumnId }}" />
    @endif

    @if ($editingTaskId)
        <livewire:task.edit :task-id="$editingTaskId" wire:key="task-edit-{{ $editingTaskId }}" />
    @endif

    @if ($confirmingDeleteColumnId)
        @php $columnToDelete = $board->columns->firstWhere('id', $confirmingDeleteColumnId) @endphp
        @if ($columnToDelete)
            <x-modal title="Excluir coluna" max-width="max-w-sm">
                <p class="text-sm text-ink-muted">
                    Tem certeza que deseja excluir a coluna "{{ $columnToDelete->name }}"? Essa ação não pode ser desfeita.
                </p>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button variant="secondary" wire:click="$dispatch('close-modal')">Cancelar</x-button>
                    <x-button variant="danger" wire:click="deleteColumn({{ $columnToDelete->id }})">Excluir</x-button>
                </div>
            </x-modal>
        @endif
    @endif
</div>
