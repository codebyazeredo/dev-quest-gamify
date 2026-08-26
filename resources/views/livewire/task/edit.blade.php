<x-modal title="Editar tarefa">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="edit-task-title" class="block text-sm font-medium text-ink">Título</label>
            <input id="edit-task-title" type="text" wire:model="title" required
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('title') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="edit-task-description" class="block text-sm font-medium text-ink">Descrição</label>
            <textarea id="edit-task-description" wire:model="description" rows="3"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="edit-task-category" class="block text-sm font-medium text-ink">Categoria</label>
                <select id="edit-task-category" wire:model="category_id" @disabled($locked) class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30 disabled:bg-line/10 disabled:text-ink-muted">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="edit-task-priority" class="block text-sm font-medium text-ink">Prioridade</label>
                <select id="edit-task-priority" wire:model="priority_id" @disabled($locked) class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30 disabled:bg-line/10 disabled:text-ink-muted">
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->id }}">{{ $priorityOption->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($locked)
            <p class="text-xs text-ink-muted">Categoria e prioridade estão bloqueadas porque esta tarefa já foi concluída.</p>
        @endif

        <div>
            <label for="edit-task-due-at" class="block text-sm font-medium text-ink">Prazo</label>
            <input id="edit-task-due-at" type="datetime-local" wire:model="due_at"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('due_at') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Salvar
            </button>
        </div>
    </form>
</x-modal>
