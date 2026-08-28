<?php

namespace App\Services;

use App\Enums\TaskEventType;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Events\TaskCompleted;
use App\Events\TaskEventCreated;
use App\Exceptions\MissingMilestoneColumnException;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskEvent;
use App\Models\TaskEventRule;
use App\Models\TaskMovement;
use App\Models\TaskPriority;
use App\Models\User;
use App\Repositories\BoardColumnRepository;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private XpService $xpService,
        private BoardColumnRepository $columns,
    ) {}

    public function create(array $data, User $creator): Task
    {
        return DB::transaction(function () use ($data, $creator) {
            $category = TaskCategory::findOrFail((int) $data['category_id']);
            $column = BoardColumn::findOrFail((int) $data['column_id']);
            $priority = TaskPriority::findOrFail((int) $data['priority_id']);

            $position = Task::where('column_id', $column->id)->count();

            return Task::create([
                'board_id' => $column->board_id,
                'column_id' => $column->id,
                'category_id' => $category->id,
                'priority_id' => $priority->id,
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => $creator->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $column->status,
                'position' => $position,
                'base_points' => $category->base_points,
                'priority_multiplier' => $priority->multiplier,
                'due_at' => $data['due_at'] ?? null,
            ]);
        });
    }

    public function move(Task $task, BoardColumn $destination, int $position, User $actor, ?string $note = null): Task
    {
        return DB::transaction(function () use ($task, $destination, $position, $actor, $note) {
            $previousStatus = $task->status;
            $previousColumnId = $task->column_id;
            $changingColumn = $previousColumnId !== $destination->id;

            if ($previousStatus === TaskStatus::TODO && $destination->status !== TaskStatus::TODO) {
                $task->rejection_reason = null;
                $task->rejected_at = null;
            }

            if (! $changingColumn) {
                $this->reorderWithinColumn($task, $position);
            } else {
                $this->closeGap($task->column_id, $task->position);

                $position = min($position, $destination->tasks()->count());
                $this->openSlot($destination->id, $position);

                $task->column_id = $destination->id;
                $task->position = $position;
                $task->status = $destination->status;
            }

            $this->applyLifecycleSideEffects($task, $destination);

            $task->save();
            $task->refresh();

            if ($changingColumn) {
                TaskMovement::create([
                    'task_id' => $task->id,
                    'from_column_id' => $previousColumnId,
                    'to_column_id' => $destination->id,
                    'user_id' => $actor->id,
                    'note' => $note,
                    'created_at' => now(),
                ]);
            }

            $this->recordLifecycleEvents($task, $destination, $previousStatus, $actor);

            return $task;
        });
    }

    public function approve(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $destination = $this->columnTaggedWith($task->board, TaskStatus::APPROVED);

            if ($destination === null) {
                throw new MissingMilestoneColumnException('Este quadro não tem uma coluna marcada como "Aprovado".');
            }

            $task->rejection_reason = null;
            $task->rejected_at = null;
            $task->approved_by = $actor->id;

            $moved = $this->move($task, $destination, $destination->tasks()->count(), $actor);

            $this->recordEventOnce($moved, TaskEventType::APPROVED, $actor);

            return $moved->refresh();
        });
    }

    public function reject(Task $task, User $actor, string $reason): Task
    {
        return DB::transaction(function () use ($task, $actor, $reason) {
            $destination = $this->columnTaggedWith($task->board, TaskStatus::TODO);

            if ($destination === null) {
                throw new MissingMilestoneColumnException('Este quadro não tem uma coluna marcada como "A Fazer".');
            }

            $task->rejection_reason = $reason;
            $task->rejected_at = now();
            $task->approved_by = null;

            return $this->move($task, $destination, $destination->tasks()->count(), $actor, $reason);
        });
    }

    private function columnTaggedWith(Board $board, TaskStatus $status): ?BoardColumn
    {
        return $this->columns->findTaggedWith($board, $status);
    }

    public function assign(Task $task, ?User $user): Task
    {
        $task->update(['assigned_to' => $user?->id]);

        return $task;
    }

    public function updateDetails(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $task->title = $data['title'] ?? $task->title;
            $task->description = $data['description'] ?? $task->description;
            $task->due_at = $data['due_at'] ?? $task->due_at;

            $categoryChanged = isset($data['category_id']) && (int) $data['category_id'] !== $task->category_id;
            $priorityChanged = isset($data['priority_id']) && (int) $data['priority_id'] !== $task->priority_id;

            if (($categoryChanged || $priorityChanged) && $task->completed_at === null) {
                if ($categoryChanged) {
                    $category = TaskCategory::findOrFail((int) $data['category_id']);
                    $task->category_id = $category->id;
                    $task->base_points = $category->base_points;
                }

                if ($priorityChanged) {
                    $priority = TaskPriority::findOrFail((int) $data['priority_id']);
                    $task->priority_id = $priority->id;
                    $task->priority_multiplier = (string) $priority->multiplier;
                }
            }

            $task->save();

            return $task;
        });
    }

    public function markHomologationCompleted(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $this->recordEventOnce($task, TaskEventType::HOMOLOGATION_COMPLETED, $actor);

            $done = $this->columnTaggedWith($task->board, TaskStatus::DONE);

            if ($done !== null && $task->column_id !== $done->id) {
                $this->move($task, $done, $done->tasks()->count(), $actor);
            }

            return $task->refresh();
        });
    }

    public function archive(Task $task): Task
    {
        $task->update(['archived_at' => now()]);

        return $task;
    }

    public function unarchive(Task $task): Task
    {
        $task->update(['archived_at' => null]);

        return $task;
    }

    public function markDeployed(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $this->recordEventOnce($task, TaskEventType::DEPLOYED, $actor);

            return $task->refresh();
        });
    }

    public function recordEventOnce(Task $task, TaskEventType $type, User $actor): ?TaskEvent
    {
        if (TaskEvent::where('task_id', $task->id)->where('type', $type)->exists()) {
            return null;
        }

        $taskEvent = TaskEvent::create([
            'task_id' => $task->id,
            'type' => $type,
            'user_id' => $actor->id,
            'occurred_at' => now(),
        ]);

        event(new TaskEventCreated($taskEvent));

        return $taskEvent;
    }

    protected function recordLifecycleEvents(Task $task, BoardColumn $destination, ?TaskStatus $previousStatus, User $actor): void
    {
        $milestoneEvent = $this->milestoneEventFor($destination->status);

        if ($milestoneEvent !== null) {
            $this->recordEventOnce($task, $milestoneEvent, $actor);
        }

        if ($destination->is_final || $task->status === TaskStatus::DONE) {
            $this->recordEventOnce($task, TaskEventType::TEST_COMPLETED, $actor);
            $completedEvent = $this->recordEventOnce($task, TaskEventType::COMPLETED, $actor);

            if ($completedEvent !== null) {

                $xpAwarded = $task->isLate() ? 0 : $task->xpValue();

                if ($task->assigned_to !== null && $xpAwarded > 0) {
                    $this->xpService->grant(
                        $task->assignedTo,
                        $xpAwarded,
                        XpSourceType::TASK,
                        $task->id,
                        "Tarefa concluída - Tarefa #{$task->id}",
                    );
                }

                $this->grantDeferredTesterXp($task, $xpAwarded);
                $this->grantDeferredCreatorXp($task, $xpAwarded);

                event(new TaskCompleted($task, $actor));
            }
        }
    }

    private function grantDeferredTesterXp(Task $task, int $taskXp): void
    {
        if ($task->approved_by === null) {
            return;
        }

        $rule = TaskEventRule::where('type', TaskEventType::APPROVED)->first();
        $bonus = $this->percentageBonus($rule, $taskXp);

        if ($bonus <= 0) {
            return;
        }

        $approvalEvent = TaskEvent::where('task_id', $task->id)
            ->where('type', TaskEventType::APPROVED)
            ->first();

        $this->xpService->grant(
            $task->approvedBy,
            $bonus,
            XpSourceType::TASK_EVENT,
            $approvalEvent?->id ?? $task->id,
            "Aprovação de teste - Tarefa #{$task->id}",
        );
    }

    private function grantDeferredCreatorXp(Task $task, int $taskXp): void
    {
        $rule = TaskEventRule::where('type', TaskEventType::CREATION_COMPLETED)->first();
        $bonus = $this->percentageBonus($rule, $taskXp);

        if ($bonus <= 0) {
            return;
        }

        $creationEvent = $this->recordEventOnce($task, TaskEventType::CREATION_COMPLETED, $task->createdBy);

        $this->xpService->grant(
            $task->createdBy,
            $bonus,
            XpSourceType::TASK_EVENT,
            $creationEvent?->id ?? $task->id,
            "Tarefa criada foi concluída - Tarefa #{$task->id}",
        );
    }

    private function percentageBonus(?TaskEventRule $rule, int $taskXp): int
    {
        if (! $rule || ! $rule->active || $rule->xp_reward <= 0 || $taskXp <= 0) {
            return 0;
        }

        return (int) round($taskXp * ($rule->xp_reward / 100));
    }

    protected function milestoneEventFor(?TaskStatus $status): ?TaskEventType
    {
        return match ($status) {
            TaskStatus::DOING => TaskEventType::STARTED,
            TaskStatus::REVIEW => TaskEventType::DEVELOPMENT_COMPLETED,
            TaskStatus::TESTING => TaskEventType::REVIEW_COMPLETED,
            default => null,
        };
    }

    protected function closeGap(int $columnId, int $vacatedPosition): void
    {
        Task::where('column_id', $columnId)
            ->where('position', '>', $vacatedPosition)
            ->decrement('position');
    }

    protected function openSlot(int $columnId, int $position): void
    {
        Task::where('column_id', $columnId)
            ->where('position', '>=', $position)
            ->increment('position');
    }

    protected function reorderWithinColumn(Task $task, int $position): void
    {
        $columnId = $task->column_id;
        $count = Task::where('column_id', $columnId)->count();
        $position = max(0, min($position, $count - 1));

        if ($position === $task->position) {
            return;
        }

        if ($position > $task->position) {
            Task::where('column_id', $columnId)
                ->whereBetween('position', [$task->position + 1, $position])
                ->decrement('position');
        } else {
            Task::where('column_id', $columnId)
                ->whereBetween('position', [$position, $task->position - 1])
                ->increment('position');
        }

        $task->position = $position;
    }

    protected function applyLifecycleSideEffects(Task $task, BoardColumn $destination): void
    {
        if ($destination->is_final) {
            $task->completed_at ??= now();
        } else {
            $task->completed_at = null;
        }

        if ($task->started_at === null && $destination->status !== null && $destination->status !== TaskStatus::BACKLOG) {
            $task->started_at = now();
        }
    }
}
