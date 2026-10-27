<?php

namespace App\Services;

use App\Enums\TaskEventType;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Events\TaskAssigned;
use App\Events\TaskCompleted;
use App\Events\TaskEventCreated;
use App\Events\TaskMoved;
use App\Exceptions\MissingMilestoneColumnException;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\TaskEventRule;
use App\Models\User;
use App\Repositories\BoardColumnRepository;
use App\Repositories\TaskCategoryRepository;
use App\Repositories\TaskEventRepository;
use App\Repositories\TaskEventRuleRepository;
use App\Repositories\TaskMovementRepository;
use App\Repositories\TaskPriorityRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private XpService $xpService,
        private BoardColumnRepository $columns,
        private TaskRepository $tasks,
        private TaskCategoryRepository $categories,
        private TaskPriorityRepository $priorities,
        private TaskEventRepository $taskEvents,
        private TaskMovementRepository $movements,
        private TaskEventRuleRepository $eventRules,
        private UserRepository $users,
    ) {}

    public function create(array $data, User $creator): Task
    {
        return DB::transaction(function () use ($data, $creator) {
            $category = $this->categories->findOrFail((int) $data['category_id']);
            $column = $this->columns->findOrFail((int) $data['column_id']);
            $priority = $this->priorities->findOrFail((int) $data['priority_id']);

            $position = $this->tasks->countInColumn($column->id);

            return $this->tasks->create([
                'board_id' => $column->board_id,
                'column_id' => $column->id,
                'category_id' => $category->id,
                'priority_id' => $priority->id,
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => $creator->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'branch_name' => $data['branch_name'] ?? null,
                'pull_request_url' => $data['pull_request_url'] ?? null,
                'status' => $column->status,
                'position' => $position,
                'base_points' => $category->base_points,
                'priority_multiplier' => $priority->multiplier,
                'due_at' => $data['due_at'] ?? null,
            ]);
        });
    }

    public function explainMoveDenial(Task $task, BoardColumn $destination, User $actor): string
    {
        if ($destination->status === TaskStatus::APPROVED) {
            return 'A coluna "Aprovado" só é alcançada aprovando a tarefa em teste.';
        }

        if ($destination->status === TaskStatus::DONE) {
            return 'A coluna "Concluído" só é alcançada marcando homologação e implantação na tarefa aprovada.';
        }

        if ($task->status === TaskStatus::TESTING) {
            return 'Tarefas em teste só saem por aprovação ou reprovação do testador.';
        }

        if ($task->assigned_to !== null && $task->assigned_to !== $actor->id) {
            return 'Esta tarefa pertence a '.($task->assignedTo?->name ?? 'outra pessoa').'. Só o responsável pode movê-la.';
        }

        return 'Você não tem permissão para mover esta tarefa para esta coluna.';
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
                $this->tasks->closeGap($task->column_id, $task->position);

                $position = min($position, $this->tasks->countInColumn($destination->id));
                $this->tasks->openSlot($destination->id, $position);

                $task->column_id = $destination->id;
                $task->position = $position;
                $task->status = $destination->status;

                if ($this->shouldAutoAssign($task, $destination, $actor)) {
                    $task->assigned_to = $actor->id;
                }
            }

            $this->applyLifecycleSideEffects($task, $destination);

            $this->tasks->save($task);
            $task->refresh();

            if ($changingColumn) {
                $this->movements->create([
                    'task_id' => $task->id,
                    'from_column_id' => $previousColumnId,
                    'to_column_id' => $destination->id,
                    'user_id' => $actor->id,
                    'note' => $note,
                    'created_at' => now(),
                ]);

                event(new TaskMoved($task, $destination, $actor));
            }

            $this->recordLifecycleEvents($task, $destination, $previousStatus, $actor);

            return $task;
        });
    }

    private function shouldAutoAssign(Task $task, BoardColumn $destination, User $actor): bool
    {
        return $task->assigned_to === null
            && $actor->isDeveloper()
            && $destination->status !== TaskStatus::TESTING;
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

            $moved = $this->move($task, $destination, $this->tasks->countInColumn($destination->id), $actor);

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

            return $this->move($task, $destination, $this->tasks->countInColumn($destination->id), $actor, $reason);
        });
    }

    private function columnTaggedWith(Board $board, TaskStatus $status): ?BoardColumn
    {
        return $this->columns->findTaggedWith($board, $status);
    }

    public function assignToUser(Task $task, int $userId, User $actor): Task
    {
        return $this->assign($task, $this->users->findOrFail($userId), $actor);
    }

    public function assign(Task $task, ?User $user, ?User $actor = null): Task
    {
        $this->tasks->update($task, ['assigned_to' => $user?->id]);

        if ($user !== null && $actor !== null && $user->id !== $actor->id) {
            event(new TaskAssigned($task, $user, $actor));
        }

        return $task;
    }

    public function updateDetails(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $task->title = $data['title'] ?? $task->title;
            $task->description = $data['description'] ?? $task->description;
            $task->due_at = $data['due_at'] ?? $task->due_at;

            foreach (['branch_name', 'pull_request_url'] as $gitField) {
                if (array_key_exists($gitField, $data)) {
                    $task->{$gitField} = $data[$gitField];
                }
            }

            $categoryChanged = isset($data['category_id']) && (int) $data['category_id'] !== $task->category_id;
            $priorityChanged = isset($data['priority_id']) && (int) $data['priority_id'] !== $task->priority_id;

            if (($categoryChanged || $priorityChanged) && $task->completed_at === null) {
                if ($categoryChanged) {
                    $category = $this->categories->findOrFail((int) $data['category_id']);
                    $task->category_id = $category->id;
                    $task->base_points = $category->base_points;
                }

                if ($priorityChanged) {
                    $priority = $this->priorities->findOrFail((int) $data['priority_id']);
                    $task->priority_id = $priority->id;
                    $task->priority_multiplier = (string) $priority->multiplier;
                }
            }

            return $this->tasks->save($task);
        });
    }

    public function markHomologationCompleted(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $this->recordEventOnce($task, TaskEventType::HOMOLOGATION_COMPLETED, $actor);

            $done = $this->columnTaggedWith($task->board, TaskStatus::DONE);

            if ($done !== null && $task->column_id !== $done->id) {
                $this->move($task, $done, $this->tasks->countInColumn($done->id), $actor);
            }

            return $task->refresh();
        });
    }

    public function archive(Task $task): Task
    {
        return $this->tasks->update($task, ['archived_at' => now()]);
    }

    public function unarchive(Task $task): Task
    {
        return $this->tasks->update($task, ['archived_at' => null]);
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
        if ($this->taskEvents->existsOfType($task, $type)) {
            return null;
        }

        $taskEvent = $this->taskEvents->create([
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

        $rule = $this->eventRules->findByType(TaskEventType::APPROVED);
        $bonus = $this->percentageBonus($rule, $taskXp);

        if ($bonus <= 0) {
            return;
        }

        $approvalEvent = $this->taskEvents->firstOfType($task, TaskEventType::APPROVED);

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
        $rule = $this->eventRules->findByType(TaskEventType::CREATION_COMPLETED);
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

    protected function reorderWithinColumn(Task $task, int $position): void
    {
        $columnId = $task->column_id;
        $count = $this->tasks->countInColumn($columnId);
        $position = max(0, min($position, $count - 1));

        if ($position === $task->position) {
            return;
        }

        if ($position > $task->position) {
            $this->tasks->shiftUp($columnId, $task->position + 1, $position);
        } else {
            $this->tasks->shiftDown($columnId, $position, $task->position - 1);
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
