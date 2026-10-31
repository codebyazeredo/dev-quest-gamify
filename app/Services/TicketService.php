<?php

namespace App\Services;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Exceptions\DuplicateEntryException;
use App\Mail\TicketReplied;
use App\Models\Client;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Repositories\TicketAttachmentRepository;
use App\Repositories\TicketMessageRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\Support\StoredFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TicketService
{
    private const FIRST_RESPONSE_WINDOW_DAYS = 7;

    private const CLIENT_HISTORY_LIMIT = 5;

    public function __construct(
        private UserRepository $users,
        private TicketRepository $tickets,
        private TicketMessageRepository $messages,
        private TicketAttachmentRepository $attachments,
        private TaskService $tasks,
    ) {}

    /** @param  array{view: string, status: string, priority: string, assignee: string, sort: string, search: string}  $filters */
    public function queue(array $filters, User $viewer, int $perPage): LengthAwarePaginator
    {
        return $this->tickets->paginateQueue($filters, $viewer, $perPage);
    }

    /** @return array{open: int, mine: int, unassigned: int, overdue: int, waiting: int, resolved: int, all: int, avg_first_response_minutes: ?int} */
    public function queueSummary(User $viewer): array
    {
        $responded = $this->tickets->respondedSince(now()->subDays(self::FIRST_RESPONSE_WINDOW_DAYS));

        $average = $responded->isEmpty()
            ? null
            : (int) round($responded->avg(fn (Ticket $ticket) => $ticket->created_at->diffInMinutes($ticket->first_responded_at)));

        return [...$this->tickets->countsByView($viewer), 'avg_first_response_minutes' => $average];
    }

    /** @return Collection<int, User> */
    public function assignableStaff(): Collection
    {
        return $this->users->supportStaff();
    }

    /** @return Collection<int, Ticket> */
    public function otherTicketsOf(Ticket $ticket): Collection
    {
        return $this->tickets->recentOfClient($ticket->client, $ticket, self::CLIENT_HISTORY_LIMIT);
    }

    /** @param  array{view: string, status: string, priority: string, assignee: string, sort: string, search: string}  $filters */
    public function exportRows(array $filters, User $viewer): iterable
    {
        return $this->tickets->lazyQueue($filters, $viewer)->map(fn (Ticket $ticket) => [
            $ticket->id,
            $ticket->title,
            $ticket->client->razao_social,
            $ticket->status->label(),
            $ticket->priority->label(),
            $ticket->assignedTo?->name,
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->due_at?->format('d/m/Y H:i'),
            $ticket->first_responded_at?->format('d/m/Y H:i'),
            $ticket->task_id !== null ? "DQ-{$ticket->task_id}" : null,
        ]);
    }

    public function assign(Ticket $ticket, ?int $userId): Ticket
    {
        return $this->tickets->update($ticket, ['assigned_to' => $userId]);
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
                'priority' => TicketPriority::NORMAL,
                'due_at' => now()->addHours(TicketPriority::NORMAL->slaHours()),
            ]);

            $this->attach($ticket, null, null, $files);

            return $ticket;
        });
    }

    /** @param  array<int, UploadedFile>  $files */
    public function reply(Ticket $ticket, string $body, ?User $staff = null, array $files = [], bool $internal = false): TicketMessage
    {
        return DB::transaction(function () use ($ticket, $body, $staff, $files, $internal) {
            $message = $this->messages->create([
                'ticket_id' => $ticket->id,
                'user_id' => $staff?->id,
                'body' => $body,
                'is_internal' => $internal && $staff !== null,
            ]);

            $this->attach($ticket, $message, $staff, $files);

            if ($message->is_internal) {
                return $message;
            }

            if ($staff === null && $ticket->status === TicketStatus::RESOLVED) {
                $this->changeStatus($ticket, TicketStatus::OPEN);
            }

            if ($staff === null && $ticket->status === TicketStatus::WAITING_CUSTOMER) {
                $this->changeStatus($ticket, TicketStatus::IN_PROGRESS);
            }

            if ($staff !== null && $ticket->status === TicketStatus::OPEN) {
                $this->changeStatus($ticket, TicketStatus::IN_PROGRESS);
            }

            if ($staff !== null) {
                $this->registerStaffResponse($ticket, $staff);

                Mail::to($ticket->client->email)->send(new TicketReplied($ticket, $message));
            }

            return $message;
        });
    }

    private function registerStaffResponse(Ticket $ticket, User $staff): void
    {
        $changes = array_filter([
            'first_responded_at' => $ticket->first_responded_at === null ? now() : null,
            'assigned_to' => $ticket->assigned_to === null ? $staff->id : null,
        ]);

        if ($changes !== []) {
            $this->tickets->update($ticket, $changes);
        }
    }

    public function changeStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        return $this->tickets->update($ticket, ['status' => $status]);
    }

    public function changePriority(Ticket $ticket, TicketPriority $priority): Ticket
    {
        return $this->tickets->update($ticket, [
            'priority' => $priority,
            'due_at' => $ticket->created_at->copy()->addHours($priority->slaHours()),
        ]);
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
