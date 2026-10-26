<x-layouts.app>
    <div>
        <x-page-header title="Ranking" subtitle="Quem mais acumulou XP no período.">
            <div class="btn-group" role="group" aria-label="Período">
                @foreach (['week' => 'Semanal', 'month' => 'Mensal', 'total' => 'Total'] as $periodKey => $periodLabel)
                    <a href="{{ route('ranking', ['role' => $activeRole, 'period' => $periodKey, 'porPagina' => request('porPagina')]) }}"
                        class="btn {{ $period === $periodKey ? 'btn-primary' : 'btn-outline-secondary bg-body' }}">
                        {{ $periodLabel }}
                    </a>
                @endforeach
            </div>
        </x-page-header>

        <ul class="nav nav-pills gap-2 mb-3">
            @foreach (['dev' => 'Desenvolvedores', 'tester' => 'Testes', 'suporte' => 'Suporte'] as $roleKey => $roleLabel)
                <li class="nav-item">
                    <a href="{{ route('ranking', ['role' => $roleKey, 'period' => $period, 'porPagina' => request('porPagina')]) }}"
                        class="nav-link {{ $activeRole === $roleKey ? 'active' : 'bg-body border' }}">
                        {{ $roleLabel }}
                    </a>
                </li>
            @endforeach
        </ul>

        <x-data-table :paginator="$users">
            <x-slot:head>
                <th class="ps-4" style="width: 5rem;">#</th>
                <th>Nome</th>
                <th class="text-end pe-4">
                    XP
                    <span class="fw-normal text-lowercase">
                        ({{ match ($period) { 'week' => 'na semana', 'month' => 'no mês', default => 'total' } }})
                    </span>
                </th>
            </x-slot:head>

            @forelse ($users as $index => $user)
                @php
                    $rank = $users->firstItem() + $index;
                    $medalColor = match ($rank) {
                        1 => 'warning',
                        2 => 'secondary',
                        3 => 'danger',
                        default => null,
                    };
                @endphp
                <tr class="{{ $user->id === auth()->id() ? 'table-primary fw-semibold' : '' }}">
                    <td class="ps-4">
                        @if ($medalColor)
                            <x-icon-chip icon="medal" :color="$medalColor" size="sm" />
                        @else
                            <span class="text-body-secondary ps-2">{{ $rank }}º</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-avatar :user="$user" />
                            {{ $user->name }}
                            @if ($user->id === auth()->id())
                                <x-badge color="primary">Você</x-badge>
                            @endif
                        </div>
                    </td>
                    <td class="text-end pe-4 fw-semibold">{{ number_format($user->total_xp ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-body-secondary py-5">Ninguém neste ranking ainda.</td>
                </tr>
            @endforelse
        </x-data-table>
    </div>
</x-layouts.app>
