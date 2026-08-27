<x-modal title="Nova tarefa">
    <form wire:submit="save" class="space-y-4">
        <x-input name="title" label="Título" wire:model="title" required autofocus />

        <x-textarea name="description" label="Descrição" wire:model="description" />

        <div class="grid grid-cols-2 gap-4">
            <x-select name="category_id" label="Categoria" wire:model="category_id" placeholder="Selecionar...">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-select>

            <x-select name="priority_id" label="Prioridade" wire:model="priority_id" placeholder="Selecionar...">
                @foreach ($priorities as $priorityOption)
                    <option value="{{ $priorityOption->id }}">{{ $priorityOption->name }}</option>
                @endforeach
            </x-select>
        </div>

        @if ($developers->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-ink">Atribuir a</label>
                <div class="mt-1">
                    <x-user-picker :users="$developers" model="assigned_to" />
                </div>
            </div>
        @endif

        <x-input name="due_at" label="Prazo" type="datetime-local" wire:model="due_at" />

        <div class="flex justify-end gap-2">
            <x-button variant="secondary" wire:click="cancel">Cancelar</x-button>
            <x-button type="submit">Criar tarefa</x-button>
        </div>
    </form>
</x-modal>
