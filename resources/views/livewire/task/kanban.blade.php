<div class="d-flex flex-column flex-grow-1 min-h-0" x-data="kanbanBoard" x-on:dragend.window="endDrag()">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <div class="input-group" style="max-width: 16rem;">
            <input type="search" x-model.debounce.150ms="search" class="form-control" placeholder="Buscar tarefas" aria-label="Buscar tarefas">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
        </div>

        <select x-model="category" class="form-select w-auto" aria-label="Filtrar por categoria">
            <option value="">Todas as categorias</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <select x-model="priority" class="form-select w-auto" aria-label="Filtrar por prioridade">
            <option value="">Todas as prioridades</option>
            @foreach ($priorities as $priority)
                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
            @endforeach
        </select>

        @php
            $assignees = $board->columns->flatMap->tasks->pluck('assignedTo')->filter()->unique('id')->sortBy('name');
        @endphp

        <select x-model="assignee" class="form-select w-auto" aria-label="Filtrar por responsável">
            <option value="">Todos os responsáveis</option>
            <option value="none">Sem responsável</option>
            @foreach ($assignees as $assignee)
                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
            @endforeach
        </select>

        <button type="button" class="btn btn-icon" :class="mine ? 'btn-primary' : 'btn-outline-secondary bg-body'" x-on:click="mine = ! mine" :aria-pressed="mine" data-bs-toggle="tooltip" data-bs-title="Minhas tarefas" aria-label="Minhas tarefas">
            <i class="bi bi-person-check" aria-hidden="true"></i>
        </button>

        <button type="button" class="btn btn-icon" :class="late ? 'btn-danger' : 'btn-outline-secondary bg-body'" x-on:click="late = ! late" :aria-pressed="late" data-bs-toggle="tooltip" data-bs-title="Somente atrasadas" aria-label="Somente atrasadas">
            <i class="bi bi-alarm" aria-hidden="true"></i>
        </button>

        <button type="button" class="btn btn-icon btn-outline-danger" x-show="hasFilters" x-cloak x-on:click="clearFilters()" data-bs-toggle="tooltip" data-bs-title="Limpar filtros" aria-label="Limpar filtros">
            <i class="bi bi-funnel" aria-hidden="true"></i>
        </button>
    </div>

    <div class="kanban-board d-flex flex-grow-1 min-h-0 gap-3 overflow-x-auto pb-2" x-ref="board">
        @foreach ($board->columns as $column)
            <section
                class="kanban-column d-flex flex-column p-2"
                :class="{ 'kanban-column--over': overColumnId === {{ $column->id }} }"
                x-on:dragover="dragOver($event, {{ $column->id }}, $el)"
                x-on:dragleave="leaveColumn($event, $el)"
                x-on:drop.prevent="drop({{ $column->id }})"
                wire:key="column-{{ $column->id }}"
            >
                <header class="d-flex align-items-center gap-1 px-1 pb-2 flex-shrink-0">
                    <div class="flex-grow-1 min-w-0">
                        <input
                            type="text"
                            value="{{ $column->name }}"
                            wire:change="renameColumn({{ $column->id }}, $event.target.value)"
                            aria-label="Nome da coluna"
                            @can('update', $board) @else disabled @endcan
                            class="form-control form-control-sm kanban-column-name kanban-column-title px-2"
                        >
                        @if ($column->status && $column->status->label() !== $column->name)
                            <x-badge color="primary" class="mt-1 ms-1">{{ $column->status->label() }}</x-badge>
                        @endif
                    </div>

                    <span class="kanban-count flex-shrink-0" x-text="countVisible($el.closest('section'))">{{ $column->tasks->count() }}</span>

                    @can('update', $board)
                        <div class="position-relative flex-shrink-0" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open" title="Configurações da coluna" aria-label="Configurações da coluna" class="btn btn-sm btn-link text-body-secondary btn-icon btn-icon-sm">
                                <x-icon name="gear" />
                            </button>

                            <div x-show="open" x-cloak x-transition class="dropdown-menu dropdown-menu-end show p-3 shadow" style="width: 16rem;">
                                <label class="form-label small text-body-secondary mb-1">Marco de pontuação</label>
                                <select
                                    wire:change="setMilestone({{ $column->id }}, $event.target.value === '' ? null : $event.target.value)"
                                    class="form-select form-select-sm"
                                >
                                    <option value="" @selected(! $column->status)>Nenhum (livre)</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" @selected($column->status === $status)>{{ $status->label() }}</option>
                                    @endforeach
                                </select>

                                <div class="btn-group w-100 mt-3" role="group" aria-label="Mover coluna">
                                    <button type="button" @click="open = false" wire:click="moveColumnUp({{ $column->id }})" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Mover
                                    </button>
                                    <button type="button" @click="open = false" wire:click="moveColumnDown({{ $column->id }})" class="btn btn-sm btn-outline-secondary">
                                        Mover <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <button type="button" @click="open = false" wire:click="confirmDeleteColumn({{ $column->id }})" class="btn btn-sm btn-outline-danger w-100 mt-2">
                                    <x-icon name="trash" /> Excluir lista
                                </button>
                            </div>
                        </div>
                    @endcan

                    @can('create', \App\Models\Task::class)
                        <button
                            type="button"
                            wire:click="openCreate({{ $column->id }})"
                            title="Adicionar tarefa"
                            aria-label="Adicionar tarefa"
                            class="btn btn-sm btn-link text-body-secondary btn-icon btn-icon-sm flex-shrink-0"
                        >
                            <x-icon name="plus" />
                        </button>
                    @endcan
                </header>

                <div class="kanban-cards flex-grow-1 min-h-0 overflow-y-auto vstack gap-2 px-1 pb-1">
                    @forelse ($column->tasks as $task)
                        <x-task-card :task="$task" wire:key="task-{{ $task->id }}" />
                    @empty
                        <div class="kanban-empty text-center small text-body-tertiary border border-2 rounded-3 py-4" style="border-style: dashed !important;">
                            Sem tarefas
                        </div>
                    @endforelse
                </div>

                @can('create', \App\Models\Task::class)
                    <button
                        type="button"
                        wire:click="openCreate({{ $column->id }})"
                        class="btn btn-sm btn-link text-body-secondary text-decoration-none text-start flex-shrink-0 mt-1"
                    >
                        <x-icon name="plus" /> Adicionar tarefa
                    </button>
                @endcan
            </section>
        @endforeach

        @can('update', $board)
            <div class="kanban-column kanban-column--new p-3 align-self-start">
                <form wire:submit="addColumn" class="input-group input-group-sm">
                    <input
                        type="text"
                        wire:model="newColumnName"
                        placeholder="Nova coluna"
                        aria-label="Nome da nova coluna"
                        class="form-control @error('newColumnName') is-invalid @enderror"
                    >
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="plus" /> Adicionar
                    </button>
                </form>
                @error('newColumnName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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
            <x-modal title="Excluir coluna" size="sm">
                <p class="text-body-secondary mb-0">
                    Tem certeza que deseja excluir a coluna "{{ $columnToDelete->name }}"? Essa ação não pode ser desfeita.
                </p>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <x-button variant="secondary" wire:click="$dispatch('close-modal')">Cancelar</x-button>
                    <x-button variant="danger" wire:click="deleteColumn({{ $columnToDelete->id }})">Excluir</x-button>
                </div>
            </x-modal>
        @endif
    @endif
</div>
