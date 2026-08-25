<x-modal title="Edit board">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="edit-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input id="edit-name" type="text" wire:model="name" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea id="edit-description" wire:model="description" rows="2"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300">
            Active
        </label>

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="cancel" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Close
            </button>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Save
            </button>
        </div>
    </form>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-100">Columns</h3>

    @error('columns') <p class="mb-2 text-sm text-red-600">{{ $message }}</p> @enderror

    <div class="space-y-2">
        @foreach ($board->columns as $column)
            <div class="flex items-center gap-2 rounded-md border border-gray-200 p-2 dark:border-gray-700" wire:key="column-{{ $column->id }}">
                <input type="text" value="{{ $column->name }}"
                    wire:change="renameColumn({{ $column->id }}, $event.target.value)"
                    class="flex-1 rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">

                <select wire:change="setColumnStatus({{ $column->id }}, $event.target.value)" class="rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($column->status === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <button type="button" wire:click="moveColumnUp({{ $column->id }})" class="rounded px-2 py-1 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">&uarr;</button>
                <button type="button" wire:click="moveColumnDown({{ $column->id }})" class="rounded px-2 py-1 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">&darr;</button>
                <button type="button" wire:click="deleteColumn({{ $column->id }})" wire:confirm="Delete this column?" class="rounded px-2 py-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">&times;</button>
            </div>
        @endforeach
    </div>

    <div class="mt-4 flex items-center gap-2">
        <input type="text" wire:model="newColumnName" placeholder="New column name"
            class="flex-1 rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">

        <select wire:model="newColumnStatus" class="rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
            @endforeach
        </select>

        <button type="button" wire:click="addColumn" class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">
            Add
        </button>
    </div>
</x-modal>
