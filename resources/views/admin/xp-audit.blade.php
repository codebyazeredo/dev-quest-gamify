<x-layouts.app>
    <div>
        <x-page-header title="Auditoria de XP" subtitle="Toda concessão ou dedução de XP: quem, quanto, por quê, quando e em qual tarefa." />

        <form method="GET" action="{{ route('admin.xp-audit') }}" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="porPagina" value="{{ request('porPagina') }}">

            <div class="col-md-3">
                <label for="audit-user" class="form-label small fw-medium mb-1">Usuário</label>
                <select id="audit-user" name="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected($filters['user_id'] === $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="audit-source" class="form-label small fw-medium mb-1">Origem</label>
                <select id="audit-source" name="source" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($sources as $source)
                        <option value="{{ $source->value }}" @selected($filters['source'] === $source->value)>{{ $source->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label for="audit-from" class="form-label small fw-medium mb-1">De</label>
                <input type="date" id="audit-from" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>

            <div class="col-6 col-md-2">
                <label for="audit-to" class="form-label small fw-medium mb-1">Até</label>
                <input type="date" id="audit-to" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <x-button type="submit">Filtrar</x-button>
                <a href="{{ route('admin.xp-audit') }}" class="btn btn-outline-secondary bg-body">Limpar</a>
                <a href="{{ route('admin.xp-audit.export', array_filter($filters)) }}" class="btn btn-outline-secondary bg-body text-nowrap"><i class="bi bi-download" aria-hidden="true"></i> CSV</a>
            </div>
        </form>

        <p class="small text-body-secondary">
            Saldo do filtro: <strong class="text-body">{{ number_format($total) }} XP</strong>
            &middot; {{ number_format($transactions->total()) }} lançamento(s)
        </p>

        <x-data-table :paginator="$transactions">
            <x-slot:head>
                <th class="ps-4">Quando</th>
                <th>Usuário</th>
                <th>XP</th>
                <th>Origem</th>
                <th>Motivo</th>
                <th class="pe-4">Tarefa</th>
            </x-slot:head>

            @forelse ($transactions as $transaction)
                <tr>
                    <td class="ps-4 text-nowrap small">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->user?->name ?? 'Usuário removido' }}</td>
                    <td><x-xp-badge :amount="$transaction->amount" /></td>
                    <td><x-badge>{{ $transaction->source_type->label() }}</x-badge></td>
                    <td class="small">{{ $transaction->description }}</td>
                    <td class="pe-4">
                        @if ($taskIds->has($transaction->id))
                            <a href="{{ route('tasks.show', $taskIds[$transaction->id]) }}">#{{ $taskIds[$transaction->id] }}</a>
                        @else
                            <span class="text-body-secondary">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-body-secondary py-5">Nenhum lançamento encontrado.</td>
                </tr>
            @endforelse
        </x-data-table>
    </div>
</x-layouts.app>
