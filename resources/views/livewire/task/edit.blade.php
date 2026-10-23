<x-modal title="Editar tarefa">
    <form wire:submit="save" class="vstack gap-3">
        <x-input id="edit-task-title" name="title" label="Título" wire:model="title" required />

        <x-textarea id="edit-task-description" name="description" label="Descrição" wire:model="description" />

        <div class="row g-3">
            <div class="col-sm-6">
                <x-select id="edit-task-category" name="category_id" label="Categoria" wire:model="category_id" @disabled($locked)>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="col-sm-6">
                <x-select id="edit-task-priority" name="priority_id" label="Prioridade" wire:model="priority_id" @disabled($locked)>
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->id }}">{{ $priorityOption->name }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        @if ($locked)
            <p class="small text-body-secondary mb-0">
                <i class="bi bi-lock" aria-hidden="true"></i>
                Categoria e prioridade estão bloqueadas porque esta tarefa já foi concluída.
            </p>
        @endif

        <x-input id="edit-task-due-at" name="due_at" label="Prazo" type="datetime-local" wire:model="due_at" />

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Salvar</x-button>
        </div>
    </form>
</x-modal>
