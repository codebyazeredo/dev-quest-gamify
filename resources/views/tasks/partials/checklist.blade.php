@php
    $canEditChecklist = auth()->user()->can('create', [\App\Models\TaskChecklistItem::class, $task]);
    $completedItems = $task->checklistItems->filter->isCompleted()->count();
@endphp

<div class="card" x-data="taskChecklist({{ $task->id }})">
    <div class="card-header bg-body fw-semibold">
        <i class="bi bi-check2-square me-1" aria-hidden="true"></i> Checklist
        <x-badge>{{ $completedItems }}/{{ $task->checklistItems->count() }}</x-badge>
    </div>

    @if ($task->checklistItems->isNotEmpty())
        <div class="px-3 pt-3">
            <x-progress-bar :value="$completedItems" :max="$task->checklistItems->count()" :showLabel="false" />
        </div>
    @endif

    <ul class="list-group list-group-flush">
        @forelse ($task->checklistItems as $item)
            <li class="list-group-item d-flex align-items-center gap-3">
                <input type="checkbox" class="form-check-input mt-0" id="checklist-item-{{ $item->id }}" @checked($item->isCompleted()) @disabled(! $canEditChecklist) x-on:change="toggle({{ $item->id }})">

                <label for="checklist-item-{{ $item->id }}" class="flex-grow-1 mb-0 {{ $item->isCompleted() ? 'text-decoration-line-through text-body-secondary' : '' }}">{{ $item->title }}</label>

                @if ($item->isCompleted() && $item->completedBy)
                    <span class="small text-body-secondary">{{ $item->completedBy->name }}</span>
                @endif

                @if ($canEditChecklist)
                    <x-icon-button x-on:click="remove({{ $item->id }})" icon="trash3" variant="danger" label="Remover item" />
                @endif
            </li>
        @empty
            <li class="list-group-item small text-body-secondary">Nenhum item ainda.</li>
        @endforelse
    </ul>

    @if ($canEditChecklist)
        <form x-on:submit.prevent="add" class="card-footer bg-body">
            <label for="checklist-new-title" class="visually-hidden">Novo item</label>
            <div class="input-group">
                <input type="text" id="checklist-new-title" x-model="title" x-bind:class="{ 'is-invalid': firstError('title') }" class="form-control" maxlength="150" placeholder="Adicionar item...">
                <x-button type="submit" x-bind:disabled="isBusy"><i class="bi bi-plus-lg" aria-hidden="true"></i> Adicionar</x-button>
                <div class="invalid-feedback" x-text="firstError('title')"></div>
            </div>
        </form>
    @endif
</div>
