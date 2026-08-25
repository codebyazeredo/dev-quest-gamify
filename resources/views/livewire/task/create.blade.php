<x-modal title="New task">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="task-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
            <input id="task-title" type="text" wire:model="title" required autofocus
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="task-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea id="task-description" wire:model="description" rows="3"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="task-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select id="task-category" wire:model="category_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Select...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="task-priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                <select id="task-priority" wire:model="priority" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->value }}">{{ $priorityOption->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($developers->isNotEmpty())
            <div>
                <label for="task-assignee" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign to</label>
                <select id="task-assignee" wire:model="assigned_to" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Unassigned</option>
                    @foreach ($developers as $developer)
                        <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label for="task-estimated" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated points</label>
            <input id="task-estimated" type="number" min="0" wire:model="estimated_points"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Create task
            </button>
        </div>
    </form>
</x-modal>
