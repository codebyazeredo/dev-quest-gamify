<div>
    <x-page-header title="Ranking">
        <div class="flex gap-1 rounded-lg border border-line bg-card p-1">
            <button type="button" wire:click="setPeriod('week')"
                class="rounded-md px-3 py-1.5 text-sm font-medium {{ $period === 'week' ? 'bg-primary text-white' : 'text-ink-muted hover:bg-line/20' }}">
                Semanal
            </button>
            <button type="button" wire:click="setPeriod('month')"
                class="rounded-md px-3 py-1.5 text-sm font-medium {{ $period === 'month' ? 'bg-primary text-white' : 'text-ink-muted hover:bg-line/20' }}">
                Mensal
            </button>
            <button type="button" wire:click="setPeriod('total')"
                class="rounded-md px-3 py-1.5 text-sm font-medium {{ $period === 'total' ? 'bg-primary text-white' : 'text-ink-muted hover:bg-line/20' }}">
                Total
            </button>
        </div>
    </x-page-header>

    <div class="mb-4 flex flex-wrap gap-2">
        <button type="button" wire:click="setRole('dev')"
            class="rounded-lg px-4 py-2 text-sm font-medium {{ $activeRole === 'dev' ? 'bg-primary text-white' : 'border border-line text-ink hover:bg-line/20' }}">
            Desenvolvedores
        </button>
        <button type="button" wire:click="setRole('tester')"
            class="rounded-lg px-4 py-2 text-sm font-medium {{ $activeRole === 'tester' ? 'bg-primary text-white' : 'border border-line text-ink hover:bg-line/20' }}">
            Testes
        </button>
        <button type="button" wire:click="setRole('suporte')"
            class="rounded-lg px-4 py-2 text-sm font-medium {{ $activeRole === 'suporte' ? 'bg-primary text-white' : 'border border-line text-ink hover:bg-line/20' }}">
            Suporte
        </button>
    </div>

    <div class="overflow-x-auto rounded-xl border border-line bg-card">
        <table class="w-full text-sm">
            <thead class="bg-line/20 text-left text-ink-muted">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">
                        XP
                        <span class="font-normal text-ink-muted">
                            ({{ match ($period) { 'week' => 'na semana', 'month' => 'no mês', default => 'total' } }})
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/50">
                @forelse ($users as $index => $user)
                    @php
                        $rank = $users->firstItem() + $index;
                        $medalColor = match ($rank) {
                            1 => 'text-gold',
                            2 => 'text-ink-muted',
                            3 => 'text-amber-clay',
                            default => null,
                        };
                    @endphp
                    <tr wire:key="rank-{{ $user->id }}" class="{{ $user->id === auth()->id() ? 'bg-primary/10 font-semibold' : '' }}">
                        <td class="px-4 py-2 text-ink-muted">
                            @if ($medalColor)
                                <span class="{{ $medalColor }}"><x-icon name="medal" class="h-5 w-5" /></span>
                            @else
                                {{ $rank }}º
                            @endif
                        </td>
                        <td class="px-4 py-2 text-ink">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-ink">{{ number_format($user->total_xp ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-ink-muted">Ninguém neste ranking ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-per-page-selector />
        {{ $users->links() }}
    </div>
</div>
