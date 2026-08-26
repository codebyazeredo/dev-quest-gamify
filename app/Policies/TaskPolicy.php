<?php

namespace App\Policies;

use App\Enums\TaskStatus;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProductOwner() || $user->isSuporte();
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        if ($user->isSuporte()) {
            return $task->status === TaskStatus::BACKLOG;
        }

        return false;
    }

    public function assignAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function claim(User $user, Task $task): bool
    {
        return $user->isDeveloper() && $task->assigned_to === null;
    }

    /**
     * A move that takes the task from before Testing to Testing-or-beyond is the
     * review sign-off (see §9: developer "não pode revisar a própria tarefa") —
     * the assignee cannot perform it themselves; any other developer can.
     *
     * Testing is a checkpoint, not a regular column: nobody exits it through a
     * generic drag, and nobody enters "Aprovado" that way either — both only
     * happen through the dedicated approve()/reject() actions below. "Concluído"
     * is the same story: nobody drags into it — it's only reached automatically
     * once both homologação and implantação are marked on an approved task (see
     * TaskService::completeIfReady()).
     */
    public function move(User $user, Task $task, BoardColumn $destination): bool
    {
        if ($destination->status === TaskStatus::APPROVED) {
            return false;
        }

        if ($destination->status === TaskStatus::DONE) {
            return false;
        }

        if ($task->status === TaskStatus::TESTING && $destination->status !== TaskStatus::TESTING) {
            return false;
        }

        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        if (! $user->isDeveloper()) {
            return false;
        }

        $isSignOff = $task->status->value < TaskStatus::TESTING->value
            && $destination->status === TaskStatus::TESTING;

        if ($isSignOff) {
            return $task->assigned_to !== $user->id;
        }

        return $task->assigned_to === $user->id;
    }

    /**
     * Approving/rejecting out of Testing is the tester's anti-cheat checkpoint:
     * even a user who is both Dev and Tester cannot sign off on their own
     * assigned task (see §6 — role stacking must not bypass this rule).
     */
    public function approve(User $user, Task $task): bool
    {
        return $this->canApproveOrReject($user, $task);
    }

    public function reject(User $user, Task $task): bool
    {
        return $this->canApproveOrReject($user, $task);
    }

    private function canApproveOrReject(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        return $user->isTester() && $task->assigned_to !== $user->id;
    }

    /**
     * Marking homologação is what completes the task (see
     * TaskService::markHomologationCompleted()) — only makes sense once a
     * tester has signed off, i.e. a task currently sitting in "Aprovado".
     */
    public function markHomologationCompleted(User $user, Task $task): bool
    {
        return $task->status === TaskStatus::APPROVED && $this->canActOnOwnTask($user, $task);
    }

    /**
     * Implantação is tracked after the fact, once the task is already done —
     * it no longer gates anything, so it only applies to a task sitting in
     * "Concluído".
     */
    public function markDeployed(User $user, Task $task): bool
    {
        return $task->status === TaskStatus::DONE && $this->canActOnOwnTask($user, $task);
    }

    private function canActOnOwnTask(User $user, Task $task): bool
    {
        if ($user->isAdmin() || $user->isProductOwner()) {
            return true;
        }

        return $user->isDeveloper() && $task->assigned_to === $user->id;
    }
}
