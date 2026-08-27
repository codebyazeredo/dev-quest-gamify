<x-modal title="Nova tarefa">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="task-title" class="block text-sm font-medium text-ink">Título</label>
            <input id="task-title" type="text" wire:model="title" required autofocus
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('title') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="task-description" class="block text-sm font-medium text-ink">Descrição</label>
            <textarea id="task-description" wire:model="description" rows="3"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="task-category" class="block text-sm font-medium text-ink">Categoria</label>
                <select id="task-category" wire:model="category_id" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                    <option value="">Selecionar...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="task-priority" class="block text-sm font-medium text-ink">Prioridade</label>
                <select id="task-priority" wire:model="priority_id" class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                    <option value="">Selecionar...</option>
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->id }}">{{ $priorityOption->name }}</option>
                    @endforeach
                </select>
                @error('priority_id') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
            </div>
        </div>

        @if ($developers->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-ink">Atribuir a</label>
                <div class="mt-1">
                    <x-user-picker :users="$developers" model="assigned_to" />
                </div>
            </div>
        @endif

        <div>
            <label for="task-due-at" class="block text-sm font-medium text-ink">Prazo</label>
            <input id="task-due-at" type="datetime-local" wire:model="due_at"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            @error('due_at') <p class="mt-1 text-sm text-terracotta">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-line/20">
                Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                Criar tarefa
            </button>
        </div>
    </form>
</x-modal>
