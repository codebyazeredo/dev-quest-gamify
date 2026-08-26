<div>
    <h1 class="mb-4 text-xl font-semibold text-gray-800 dark:text-gray-100">Ranking</h1>

    <div class="mb-6 flex gap-2">
        <button type="button" wire:click="setRole('dev')"
            class="rounded-md px-4 py-2 text-sm font-medium {{ $activeRole === 'dev' ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700' }}">
            Desenvolvedores
        </button>
        <button type="button" wire:click="setRole('tester')"
            class="rounded-md px-4 py-2 text-sm font-medium {{ $activeRole === 'tester' ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700' }}">
            Testes
        </button>
        <button type="button" wire:click="setRole('suporte')"
            class="rounded-md px-4 py-2 text-sm font-medium {{ $activeRole === 'suporte' ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700' }}">
            Suporte
        </button>
    </div>

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
                @forelse ($users as $index => $user)
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
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Ninguém neste ranking ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
