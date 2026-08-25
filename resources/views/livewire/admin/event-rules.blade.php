<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Event Rules</h1>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">Event</th>
                    <th class="px-4 py-2">XP reward</th>
                    <th class="px-4 py-2">Active</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($rules as $rule)
                    <tr wire:key="rule-{{ $rule->id }}">
                        @if ($editingId === $rule->id)
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $rule->type->label() }}</td>
                            <td class="px-4 py-2">
                                <input type="number" min="0" wire:model="editingXpReward" class="w-24 rounded-md border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </td>
                            <td class="px-4 py-2">
                                <input type="checkbox" wire:model="editingActive" class="rounded border-gray-300">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="update" class="text-indigo-600 hover:underline">Save</button>
                                <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 hover:underline">Cancel</button>
                            </td>
                        @else
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $rule->type->label() }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $rule->xp_reward }}</td>
                            <td class="px-4 py-2">
                                @if ($rule->active)
                                    <span class="text-green-600">Active</span>
                                @else
                                    <span class="text-gray-400">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" wire:click="edit({{ $rule->id }})" class="text-indigo-600 hover:underline">Edit</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
