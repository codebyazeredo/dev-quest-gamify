<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Ranking</h1>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">XP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($users as $index => $user)
                    <tr wire:key="rank-{{ $user->id }}">
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400">
                            {{ $users->firstItem() + $index }}º
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ number_format($user->total_xp ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
