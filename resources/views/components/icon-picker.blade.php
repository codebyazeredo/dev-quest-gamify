@props(['model', 'icons'])

<div class="flex flex-wrap gap-2">
    @foreach ($icons as $iconName)
        <label class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-md border border-gray-300 text-gray-500 hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-600 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:has-[:checked]:border-indigo-400 dark:has-[:checked]:bg-indigo-900/30 dark:has-[:checked]:text-indigo-300">
            <input type="radio" wire:model="{{ $model }}" value="{{ $iconName }}" class="sr-only">
            <x-icon :name="$iconName" class="h-6 w-6" />
        </label>
    @endforeach
</div>
