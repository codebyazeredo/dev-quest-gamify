<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Exceptions\DuplicateEntryException;
use App\Models\Client;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Repositories\TicketMessageRepository;
use App\Repositories\TicketRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        private TicketRepository $tickets,
        private TicketMessageRepository $messages,
        private TaskService $tasks,
    ) {}

    public function paginateForStaff(int $perPage, string $status, string $search): LengthAwarePaginator
    {
        return $this->tickets->paginateFiltered($perPage, $status, $search);
    }

    public function paginateForClient(Client $client, int $perPage): LengthAwarePaginator
    {
        return $this->tickets->paginateForClient($client, $perPage);
    }

    public function findForClient(Client $client, int $ticketId): Ticket
    {
        return $this->tickets->findForClient($client, $ticketId);
    }

    public function open(Client $client, array $data): Ticket
    {
        return $this->tickets->create([
            'client_id' => $client->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => TicketStatus::OPEN,
        ]);
    }

    public function reply(Ticket $ticket, string $body, ?User $staff = null): TicketMessage
    {
        return DB::transaction(function () use ($ticket, $body, $staff) {
            $message = $this->messages->create(['ticket_id' => $ticket->id, 'user_id' => $staff?->id, 'body' => $body]);

            if ($staff === null && $ticket->status === TicketStatus::RESOLVED) {
                $this->changeStatus($ticket, TicketStatus::OPEN);
            }

            if ($staff !== null && $ticket->status === TicketStatus::OPEN) {
                $this->changeStatus($ticket, TicketStatus::IN_PROGRESS);
            }

            return $message;
        });
    }

    public function changeStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        return $this->tickets->update($ticket, ['status' => $status]);
    }

    public function convertToTask(Ticket $ticket, array $taskData, User $staff): Task
    {
        if ($ticket->task_id !== null) {
            throw new DuplicateEntryException('Este chamado já foi convertido em tarefa.');
        }

        return DB::transaction(function () use ($ticket, $taskData, $staff) {
            $task = $this->tasks->create([
                ...$taskData,
                'title' => $ticket->title,
                'description' => "Chamado #{$ticket->id} — {$ticket->client->razao_social}\n\n{$ticket->description}",
            ], $staff);

            $this->tickets->update($ticket, ['task_id' => $task->id]);

            if ($ticket->status === TicketStatus::OPEN) {
                $this->changeStatus($ticket, TicketStatus::IN_PROGRESS);
            }

            return $task;
        });
    }
}
