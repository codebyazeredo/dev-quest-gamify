<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

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

    public function paginateFiltered(int $perPage, string $status, string $search): LengthAwarePaginator
    {
        return $this->query()
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('client', fn (Builder $client) => $client->where('razao_social', 'like', "%{$search}%"));
            }))
            ->paginate($perPage);
    }

    public function paginateForClient(Client $client, int $perPage): LengthAwarePaginator
    {
        return $client->tickets()->latest()->paginate($perPage);
    }

    public function findForClient(Client $client, int $ticketId): Ticket
    {
        return $client->tickets()->with('messages.user')->findOrFail($ticketId);
    }
}
