<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Titles</h1>

    @if ($unlockedTitles->isEmpty())
        <p class="text-sm text-gray-400">You haven't unlocked any titles yet.</p>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($unlockedTitles as $title)
                <div class="flex items-center justify-between rounded-lg border p-4 {{ $selectedTitleId === $title->id ? 'border-indigo-300 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $title->icon }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $title->name }}</span>
                    </div>

                    @if ($selectedTitleId === $title->id)
                        <button type="button" wire:click="clearTitle" class="text-xs text-gray-500 hover:underline">
                            Clear
                        </button>
                    @else
                        <button type="button" wire:click="selectTitle({{ $title->id }})" class="rounded-md bg-indigo-600 px-2 py-1 text-xs font-semibold text-white hover:bg-indigo-500">
                            Select
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
