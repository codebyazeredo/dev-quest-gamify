<?php

namespace App\Services;

use App\Enums\TaskEventType;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Events\TaskCompleted;
use App\Events\TaskEventCreated;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskEvent;
use App\Models\TaskEventRule;
use App\Models\TaskMovement;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(private XpService $xpService) {}

    /**
     * @param  array<string, mixed>  $data
     */
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

    /**
     * Move a task from Testing into the "Aprovado" checkpoint column. The only
     * way out of Testing besides reject() — see TaskPolicy::move()/approve().
     */
    public function approve(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $destination = $this->columnFor($task, TaskStatus::APPROVED);

            $task->rejection_reason = null;
            $task->rejected_at = null;
            $task->approved_by = $actor->id;

            $moved = $this->move($task, $destination, $destination->tasks()->count(), $actor);

            $this->recordEventOnce($moved, TaskEventType::APPROVED, $actor);

            return $moved->refresh();
        });
    }

    /**
     * Send a task back to "A Fazer" with a mandatory reason. The task keeps the
     * rejection badge until it leaves "A Fazer" again (see move()).
     */
    public function reject(Task $task, User $actor, string $reason): Task
    {
        return DB::transaction(function () use ($task, $actor, $reason) {
            $destination = $this->columnFor($task, TaskStatus::TODO);

            $task->rejection_reason = $reason;
            $task->rejected_at = now();
            $task->approved_by = null;

            return $this->move($task, $destination, $destination->tasks()->count(), $actor, $reason);
        });
    }

    private function columnFor(Task $task, TaskStatus $status): BoardColumn
    {
        return BoardColumn::where('board_id', $task->board_id)
            ->where('status', $status)
            ->firstOrFail();
    }

    public function assign(Task $task, ?User $user): Task
    {
        $task->update(['assigned_to' => $user?->id]);

        return $task;
    }

    /**
     * @param  array<string, mixed>  $data
     */
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

    /**
     * "Concluído" is never reached by dragging (see TaskPolicy::move()) —
     * marking homologação on an approved task is what completes it, moving it
     * there automatically and reusing move()'s existing completion side
     * effects (XP bonus, deferred tester XP, TaskCompleted event).
     */
    public function markHomologationCompleted(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $this->recordEventOnce($task, TaskEventType::HOMOLOGATION_COMPLETED, $actor);

            $done = BoardColumn::where('board_id', $task->board_id)->where('status', TaskStatus::DONE)->first();

            if ($done !== null && $task->column_id !== $done->id) {
                $this->move($task, $done, $done->tasks()->count(), $actor);
            }

            return $task->refresh();
        });
    }

    /**
     * Implantação is recorded after the task is already "Concluído" — it's a
     * tracking flag (see the "não implantado" card badge), not a transition.
     */
    public function markDeployed(Task $task, User $actor): Task
    {
        return DB::transaction(function () use ($task, $actor) {
            $this->recordEventOnce($task, TaskEventType::DEPLOYED, $actor);

            return $task->refresh();
        });
    }

    /**
     * Record a task lifecycle event exactly once. Returns null if this task
     * already has an event of this type (idempotency guard — see §27/§53).
     */
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

    protected function recordLifecycleEvents(Task $task, BoardColumn $destination, TaskStatus $previousStatus, User $actor): void
    {
        foreach ($this->thresholdsCrossed($previousStatus, $task->status) as $type) {
            $this->recordEventOnce($task, $type, $actor);
        }

        if ($destination->is_final || $task->status === TaskStatus::DONE) {
            $this->recordEventOnce($task, TaskEventType::TEST_COMPLETED, $actor);
            $completedEvent = $this->recordEventOnce($task, TaskEventType::COMPLETED, $actor);

            if ($completedEvent !== null) {
                // A late task earns nobody anything — the deal fell through,
                // so the assignee's, tester's, and creator's rewards (the
                // latter two being a % of this same figure) all zero out
                // together rather than only penalizing the assignee.
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

    /**
     * The tester who approved this task only earns their XP once the task is
     * truly finished (see §4 — "só ganha os pontos quando a tarefa for
     * terminada"), not at the moment of approval. GrantXpListener explicitly
     * skips TaskEventType::APPROVED for this reason; this is where it's
     * actually granted — as a % of the task's own value (see
     * TaskEventType::isPercentageBased()), so approving a Crítica task pays
     * more than approving a trivial one, same as the assignee's bonus does.
     */
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

    /**
     * The person who created this task (almost always Suporte, curating the
     * backlog — see TaskPolicy::create()) only earns XP once it's actually
     * built and delivered, not for the act of creating it — this is what
     * keeps the backlog from filling up with low-effort tasks just to farm
     * XP, since an unfinished or worthless task pays nothing. Also a % of
     * the task's value, same reasoning as the tester's bonus above.
     */
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

    /**
     * @return array<int, TaskEventType>
     */
    protected function thresholdsCrossed(TaskStatus $previous, TaskStatus $new): array
    {
        $ladder = [
            TaskStatus::DOING->value => TaskEventType::STARTED,
            TaskStatus::REVIEW->value => TaskEventType::DEVELOPMENT_COMPLETED,
            TaskStatus::TESTING->value => TaskEventType::REVIEW_COMPLETED,
        ];

        $events = [];

        foreach ($ladder as $threshold => $type) {
            if ($new->value >= $threshold && $previous->value < $threshold) {
                $events[] = $type;
            }
        }

        return $events;
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

        if ($task->started_at === null && $destination->status !== TaskStatus::BACKLOG) {
            $task->started_at = now();
        }
    }
}
