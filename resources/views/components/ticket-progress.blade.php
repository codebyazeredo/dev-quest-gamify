@props(['progress'])

<div>
    <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-semibold">{{ $progress['current'] }}</span>
        <span class="small text-body-secondary">{{ $progress['percent'] }}%</span>
    </div>

    <div class="progress" role="progressbar" aria-label="Andamento" aria-valuenow="{{ $progress['percent'] }}" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar {{ $progress['is_completed'] ? 'bg-success' : '' }}" style="width: {{ $progress['percent'] }}%"></div>
    </div>

    <ol class="ticket-steps list-unstyled mt-3 mb-0">
        @foreach ($progress['steps'] as $step)
            <li class="is-{{ $step['state'] }}">
                <i class="bi {{ match ($step['state']) { 'done' => 'bi-check-circle-fill', 'current' => 'bi-record-circle-fill', default => 'bi-circle' } }}" aria-hidden="true"></i>
                {{ $step['name'] }}
            </li>
        @endforeach
    </ol>

    @if ($progress['assignee'] || $progress['due_at'])
        <dl class="small mb-0 mt-3 pt-3 border-top">
            @if ($progress['assignee'])
                <div class="d-flex justify-content-between gap-3"><dt class="fw-normal text-body-secondary">Responsável</dt><dd class="mb-0">{{ $progress['assignee'] }}</dd></div>
            @endif
            @if ($progress['due_at'])
                <div class="d-flex justify-content-between gap-3"><dt class="fw-normal text-body-secondary">Previsão</dt><dd class="mb-0">{{ $progress['due_at']->format('d/m/Y') }}</dd></div>
            @endif
        </dl>
    @endif
</div>
