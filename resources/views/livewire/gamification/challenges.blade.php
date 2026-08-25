<div>
    <h1 class="mb-6 text-xl font-semibold text-gray-800 dark:text-gray-100">Desafios</h1>

    <div class="space-y-4">
        @foreach ($challenges as $row)
            @php [$challenge, $progress, $completed] = [$row['challenge'], $row['progress'], $row['completed']]; @endphp

            <div class="rounded-lg border p-4 {{ $completed ? 'border-indigo-300 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $challenge->name }}</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">termina em {{ $challenge->ends_at->format('d/m/Y') }}</span>
                </div>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $challenge->description }}</p>

                <div class="mt-3">
                    <x-progress-bar :value="min($progress, $challenge->target)" :max="$challenge->target" />
                </div>

                <p class="mt-2 text-xs {{ $completed ? 'font-semibold text-indigo-600 dark:text-indigo-300' : 'text-gray-400' }}">
                    {{ $completed ? 'Concluído' : 'Recompensa: +'.$challenge->xp_reward.' XP' }}
                </p>
            </div>
        @endforeach

        @if ($challenges->isEmpty())
            <p class="text-sm text-gray-400">Nenhum desafio ativo no momento.</p>
        @endif
    </div>
</div>
