<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\User;
use App\Repositories\TaskChecklistItemRepository;

class TaskChecklistService
{
    public function __construct(private TaskChecklistItemRepository $items) {}

    public function add(Task $task, string $title): TaskChecklistItem
    {
        return $this->items->create(['task_id' => $task->id, 'title' => $title]);
    }

    public function toggle(TaskChecklistItem $item, User $actor): TaskChecklistItem
    {
        return $this->items->update($item, $item->isCompleted()
            ? ['completed_at' => null, 'completed_by' => null]
            : ['completed_at' => now(), 'completed_by' => $actor->id]);
    }

    public function delete(TaskChecklistItem $item): void
    {
        $this->items->delete($item);
    }
}
