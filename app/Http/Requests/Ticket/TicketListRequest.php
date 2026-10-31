<?php

namespace App\Http\Requests\Ticket;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketListRequest extends FormRequest
{
    public const VIEWS = ['open', 'mine', 'unassigned', 'overdue', 'waiting', 'resolved', 'all'];

    private const DEFAULT_PER_PAGE = 10;

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'in:10,25,50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:100'],
            'view' => ['nullable', Rule::in(self::VIEWS)],
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'assignee' => ['nullable', 'string', 'regex:/^(me|none|\d+)$/'],
            'sort' => ['nullable', 'in:due,recent'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['view' => 'visão', 'status' => 'situação', 'priority' => 'prioridade', 'assignee' => 'responsável', 'sort' => 'ordenação'];
    }

    /** @return array{view: string, status: string, priority: string, assignee: string, sort: string, search: string} */
    public function filters(): array
    {
        $status = (string) $this->validated('status');
        $view = (string) ($this->validated('view') ?? 'open');

        return [
            'view' => $status !== '' && $view === 'open' ? 'all' : $view,
            'status' => $status,
            'priority' => (string) $this->validated('priority'),
            'assignee' => (string) $this->validated('assignee'),
            'sort' => (string) ($this->validated('sort') ?? 'due'),
            'search' => trim((string) $this->validated('search')),
        ];
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? self::DEFAULT_PER_PAGE);
    }
}
