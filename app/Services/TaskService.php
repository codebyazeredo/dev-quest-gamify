<?php

namespace App\Services;

use App\Enums\TaskEventType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Events\TaskCompleted;
use App\Events\TaskEventCreated;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskEvent;
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
            $priority = TaskPriority::from((int) $data['priority']);

            $position = Task::where('column_id', $column->id)->count();

            return Task::create([
                'board_id' => $column->board_id,
                'column_id' => $column->id,
                'category_id' => $category->id,
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => $creator->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $priority,
                'status' => $column->status,
                'position' => $position,
                'base_points' => $category->base_points,
                'priority_multiplier' => $priority->multiplier(),
                'estimated_points' => $data['estimated_points'] ?? null,
            ]);
        });
    }

    public function move(Task $task, BoardColumn $destination, int $position, User $actor): Task
    {
        return DB::transaction(function () use ($task, $destination, $position, $actor) {
            $previousStatus = $task->status;

            if ($task->column_id === $destination->id) {
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

            $this->recordLifecycleEvents($task, $destination, $previousStatus, $actor);

            return $task;
        });
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
            $task->estimated_points = $data['estimated_points'] ?? $task->estimated_points;

            $categoryChanged = isset($data['category_id']) && (int) $data['category_id'] !== $task->category_id;
            $priorityChanged = isset($data['priority']) && (int) $data['priority'] !== $task->priority->value;

            if (($categoryChanged || $priorityChanged) && $task->completed_at === null) {
                if ($categoryChanged) {
                    $category = TaskCategory::findOrFail((int) $data['category_id']);
                    $task->category_id = $category->id;
                    $task->base_points = $category->base_points;
                }

                if ($priorityChanged) {
                    $priority = TaskPriority::from((int) $data['priority']);
                    $task->priority = $priority;
                    $task->priority_multiplier = (string) $priority->multiplier();
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

            return $task->refresh();
        });
    }

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
                if ($task->assigned_to !== null && $task->xpValue() > 0) {
                    $this->xpService->grant(
                        $task->assignedTo,
                        $task->xpValue(),
                        XpSourceType::TASK,
                        $task->id,
                        "Task completed - Task #{$task->id}",
                    );
                }

                event(new TaskCompleted($task, $actor));
            }
        }
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
