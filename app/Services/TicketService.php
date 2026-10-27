<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Exceptions\DuplicateEntryException;
use App\Models\Client;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Repositories\TicketAttachmentRepository;
use App\Repositories\TicketMessageRepository;
use App\Repositories\TicketRepository;
use App\Support\StoredFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        private TicketRepository $tickets,
        private TicketMessageRepository $messages,
        private TicketAttachmentRepository $attachments,
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

    /** @param  array<int, UploadedFile>  $files */
    public function open(Client $client, array $data, array $files = []): Ticket
    {
        return DB::transaction(function () use ($client, $data, $files) {
            $ticket = $this->tickets->create([
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => TicketStatus::OPEN,
            ]);

            $this->attach($ticket, null, null, $files);

            return $ticket;
        });
    }

    /** @param  array<int, UploadedFile>  $files */
    public function reply(Ticket $ticket, string $body, ?User $staff = null, array $files = []): TicketMessage
    {
        return DB::transaction(function () use ($ticket, $body, $staff, $files) {
            $message = $this->messages->create(['ticket_id' => $ticket->id, 'user_id' => $staff?->id, 'body' => $body]);

            $this->attach($ticket, $message, $staff, $files);

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
                'description' => "Chamado #{$ticket->id} — {$ticket->client->razao_social}\n\n{$ticket->description}",
            ], $staff);

            $this->tickets->update($ticket, ['task_id' => $task->id]);

            if ($ticket->status === TicketStatus::OPEN) {
                $this->changeStatus($ticket, TicketStatus::IN_PROGRESS);
            }

            return $task;
        });
    }

    /** @return array<string, mixed>|null */
    public function progressFor(Ticket $ticket): ?array
    {
        $task = $ticket->task;

        if ($task === null) {
            return null;
        }

        $columns = $task->board->columns->values();
        $found = $columns->search(fn ($column) => $column->id === $task->column_id);
        $currentIndex = $found === false ? 0 : $found;
        $isCompleted = $task->completed_at !== null;

        return [
            'is_completed' => $isCompleted,
            'current' => $isCompleted ? 'Concluída' : $task->column->name,
            'percent' => $isCompleted ? 100 : (int) round((($currentIndex + 1) / max(1, $columns->count())) * 100),
            'assignee' => $task->assignedTo?->name,
            'due_at' => $task->due_at,
            'steps' => $columns->map(fn ($column, $index) => [
                'name' => $column->name,
                'state' => $isCompleted || $index < $currentIndex ? 'done' : ($index === $currentIndex ? 'current' : 'pending'),
            ])->all(),
        ];
    }

    /** @param  array<int, UploadedFile>  $files */
    private function attach(Ticket $ticket, ?TicketMessage $message, ?User $user, array $files): void
    {
        foreach ($files as $file) {
            $this->attachments->create([
                'ticket_id' => $ticket->id,
                'ticket_message_id' => $message?->id,
                'user_id' => $user?->id,
                ...StoredFile::store($file, "ticket-attachments/{$ticket->id}"),
            ]);
        }
    }
}
