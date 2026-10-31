<?php

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;

class TicketRepository extends Repository
{
    protected function model(): string
    {
        return Ticket::class;
    }

    protected function query(): Builder
    {
        return parent::query()->with('client')->latest();
    }

    /** @param  array{view: string, status: string, priority: string, assignee: string, sort: string, search: string}  $filters */
    public function paginateQueue(array $filters, User $viewer, int $perPage): LengthAwarePaginator
    {
        return $this->queueQuery($filters, $viewer)
            ->with('assignedTo.person')
            ->withCount('messages')
            ->withMax('messages as last_message_at', 'created_at')
            ->paginate($perPage);
    }

    /**
     * @param  array{view: string, status: string, priority: string, assignee: string, sort: string, search: string}  $filters
     * @return LazyCollection<int, Ticket>
     */
    public function lazyQueue(array $filters, User $viewer): LazyCollection
    {
        return $this->queueQuery($filters, $viewer)->with('assignedTo')->lazy();
    }

    /** @return array{open: int, mine: int, unassigned: int, overdue: int, waiting: int, resolved: int, all: int} */
    public function countsByView(User $viewer): array
    {
        $resolved = TicketStatus::RESOLVED->value;

        $row = parent::query()->selectRaw('
            SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) AS open_count,
            SUM(CASE WHEN status != ? AND assigned_to = ? THEN 1 ELSE 0 END) AS mine_count,
            SUM(CASE WHEN status != ? AND assigned_to IS NULL THEN 1 ELSE 0 END) AS unassigned_count,
            SUM(CASE WHEN status != ? AND first_responded_at IS NULL AND due_at < ? THEN 1 ELSE 0 END) AS overdue_count,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS waiting_count,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS resolved_count,
            COUNT(*) AS all_count
        ', [$resolved, $resolved, $viewer->id, $resolved, $resolved, now(), TicketStatus::WAITING_CUSTOMER->value, $resolved])->first();

        return [
            'open' => (int) $row->open_count,
            'mine' => (int) $row->mine_count,
            'unassigned' => (int) $row->unassigned_count,
            'overdue' => (int) $row->overdue_count,
            'waiting' => (int) $row->waiting_count,
            'resolved' => (int) $row->resolved_count,
            'all' => (int) $row->all_count,
        ];
    }

    /** @return Collection<int, Ticket> */
    public function respondedSince(CarbonInterface $since): Collection
    {
        return parent::query()
            ->where('created_at', '>=', $since)
            ->whereNotNull('first_responded_at')
            ->get(['created_at', 'first_responded_at']);
    }

    /** @return Collection<int, Ticket> */
    public function recentOfClient(Client $client, Ticket $except, int $limit): Collection
    {
        return $client->tickets()->whereKeyNot($except->id)->latest()->limit($limit)->get();
    }

    /** @param  array{view: string, status: string, priority: string, assignee: string, sort: string, search: string}  $filters */
    private function queueQuery(array $filters, User $viewer): Builder
    {
        $resolved = TicketStatus::RESOLVED->value;

        $query = $this->query()
            ->when($filters['view'] === 'open', fn (Builder $query) => $query->where('status', '!=', $resolved))
            ->when($filters['view'] === 'mine', fn (Builder $query) => $query->where('status', '!=', $resolved)->where('assigned_to', $viewer->id))
            ->when($filters['view'] === 'unassigned', fn (Builder $query) => $query->where('status', '!=', $resolved)->whereNull('assigned_to'))
            ->when($filters['view'] === 'overdue', fn (Builder $query) => $query->where('status', '!=', $resolved)->whereNull('first_responded_at')->where('due_at', '<', now()))
            ->when($filters['view'] === 'waiting', fn (Builder $query) => $query->where('status', TicketStatus::WAITING_CUSTOMER->value))
            ->when($filters['view'] === 'resolved', fn (Builder $query) => $query->where('status', $resolved))
            ->when($filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['priority'] !== '', fn (Builder $query) => $query->where('priority', $filters['priority']))
            ->when($filters['assignee'] === 'me', fn (Builder $query) => $query->where('assigned_to', $viewer->id))
            ->when($filters['assignee'] === 'none', fn (Builder $query) => $query->whereNull('assigned_to'))
            ->when(ctype_digit($filters['assignee']), fn (Builder $query) => $query->where('assigned_to', (int) $filters['assignee']))
            ->when($filters['search'] !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($filters) {
                $query->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('id', ltrim($filters['search'], '#'))
                    ->orWhereHas('client', fn (Builder $client) => $client->where('razao_social', 'like', "%{$filters['search']}%"));
            }));

        return $filters['sort'] === 'due'
            ? $query->reorder()->orderByRaw('due_at IS NULL')->orderBy('due_at')->orderByDesc('id')
            : $query;
    }

    public function paginateForClient(Client $client, int $perPage): LengthAwarePaginator
    {
        return $client->tickets()->latest()->paginate($perPage);
    }

    public function findForClient(Client $client, int $ticketId): Ticket
    {
        return $client->tickets()
            ->with([
                'attachments',
                'messages' => fn ($messages) => $messages->where('is_internal', false),
                'messages.attachments',
                'messages.user',
                'task.column',
                'task.assignedTo',
                'task.board.columns',
            ])
            ->findOrFail($ticketId);
    }
}
