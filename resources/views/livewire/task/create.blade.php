<x-modal title="Nova tarefa">
    <form wire:submit="save" class="vstack gap-3">
        <x-input name="title" label="Título" wire:model="title" required autofocus />

        <x-textarea name="description" label="Descrição" wire:model="description" />

        <div class="row g-3">
            <div class="col-sm-6">
                <x-select name="category_id" label="Categoria" wire:model="category_id" placeholder="Selecionar...">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="col-sm-6">
                <x-select name="priority_id" label="Prioridade" wire:model="priority_id" placeholder="Selecionar...">
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->id }}">{{ $priorityOption->name }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        @if ($developers->isNotEmpty())
            <div>
                <label class="form-label fw-medium small mb-1">Atribuir a</label>
                <x-user-picker :users="$developers" model="assigned_to" />
            </div>
        @endif

        <x-input name="due_at" label="Prazo" type="datetime-local" wire:model="due_at" />

        <div class="d-flex justify-content-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar tarefa</x-button>
        </div>
    </form>
</x-modal>
