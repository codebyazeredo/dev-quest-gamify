<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Ranking</h1>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">XP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($users as $index => $user)
                    @php
                        $rank = $users->firstItem() + $index;
                        $medalColor = match ($rank) {
                            1 => 'text-amber-500',
                            2 => 'text-gray-400',
                            3 => 'text-orange-700',
                            default => null,
                        };
                    @endphp
                    <tr wire:key="rank-{{ $user->id }}" class="{{ $user->id === auth()->id() ? 'bg-indigo-50 font-semibold dark:bg-indigo-900/30' : '' }}">
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400">
                            @if ($medalColor)
                                <span class="{{ $medalColor }}"><x-icon name="medal" class="h-5 w-5" /></span>
                            @else
                                {{ $rank }}º
                            @endif
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
