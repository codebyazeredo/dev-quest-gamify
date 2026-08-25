<x-modal title="Editar tarefa">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="edit-task-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
            <input id="edit-task-title" type="text" wire:model="title" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="edit-task-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
            <textarea id="edit-task-description" wire:model="description" rows="3"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="edit-task-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                <select id="edit-task-category" wire:model="category_id" @disabled($locked) class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="edit-task-priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridade</label>
                <select id="edit-task-priority" wire:model="priority" @disabled($locked) class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->value }}">{{ $priorityOption->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($locked)
            <p class="text-xs text-gray-500 dark:text-gray-400">Categoria e prioridade estão bloqueadas porque esta tarefa já foi concluída.</p>
        @endif

        <div>
            <label for="edit-task-due-at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prazo</label>
            <input id="edit-task-due-at" type="datetime-local" wire:model="due_at"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('due_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Salvar
            </button>
        </div>
    </form>
</x-modal>
