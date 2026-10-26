<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskRepository extends Repository
{
    protected function model(): string
    {
        return Task::class;
    }

    protected function active(): Builder
    {
        return $this->query()->whereNull('archived_at');
    }

    public function countInColumn(int $columnId): int
    {
        return $this->query()->where('column_id', $columnId)->count();
    }

    /** Fecha o buraco deixado por uma tarefa que saiu da posição informada. */
    public function closeGap(int $columnId, int $vacatedPosition): void
    {
        $this->query()->where('column_id', $columnId)->where('position', '>', $vacatedPosition)->decrement('position');
    }

    /** Abre espaço para uma tarefa que entra na posição informada. */
    public function openSlot(int $columnId, int $position): void
    {
        $this->query()->where('column_id', $columnId)->where('position', '>=', $position)->increment('position');
    }

    public function shiftUp(int $columnId, int $fromPosition, int $toPosition): void
    {
        $this->query()->where('column_id', $columnId)->whereBetween('position', [$fromPosition, $toPosition])->decrement('position');
    }

    public function shiftDown(int $columnId, int $fromPosition, int $toPosition): void
    {
        $this->query()->where('column_id', $columnId)->whereBetween('position', [$fromPosition, $toPosition])->increment('position');
    }

    public function countCompletedAssignedTo(User $user): int
    {
        return $this->completedAssignedTo($user)->count();
    }

    public function countCompletedBugsAssignedTo(User $user): int
    {
        return $this->completedAssignedTo($user)
            ->whereHas('category', fn (Builder $query) => $query->where('slug', 'bug'))
            ->count();
    }

    public function maxCompletedInOneDayBy(User $user): int
    {
        return (int) $this->completedAssignedTo($user)
            ->selectRaw('COUNT(*) as c')
            ->groupBy(DB::raw('DATE(completed_at)'))
            ->get()
            ->max('c');
    }

    /** Carrega tudo o que a página da tarefa exibe. */
    public function loadDetails(Task $task): Task
    {
        return $task->load([
            'board', 'column', 'category', 'priority', 'assignedTo.person', 'assignedTo.selectedTitle', 'createdBy',
            'taskEvents.user', 'taskEvents.xpTransaction',
            'movements.user', 'movements.fromColumn', 'movements.toColumn',
        ]);
    }

    protected function completedAssignedTo(User $user): Builder
    {
        return $this->query()->where('assigned_to', $user->id)->whereNotNull('completed_at');
    }

    public function openAssignedTo(User $user, int $limit): Collection
    {
        return $this->active()
            ->where('assigned_to', $user->id)
            ->whereNull('completed_at')
            ->with(['board', 'category', 'priority'])
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->limit($limit)
            ->get();
    }

    public function countOpen(): int
    {
        return $this->active()->whereNull('completed_at')->count();
    }

    public function countLate(): int
    {
        return $this->active()->whereNull('completed_at')->whereNotNull('due_at')->where('due_at', '<', now())->count();
    }

    public function countUnassigned(): int
    {
        return $this->active()->whereNull('completed_at')->whereNull('assigned_to')->count();
    }

    public function countCompletedSince(CarbonInterface $since): int
    {
        return $this->query()->whereNotNull('completed_at')->where('completed_at', '>=', $since)->count();
    }

    /** @return array<int, int> total de tarefas abertas ou em andamento por status (valor do enum => total) */
    public function countByStatus(): array
    {
        return $this->active()
            ->whereNotNull('status')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /** @return Collection<int, Task> cada item traz `total` e a categoria carregada */
    public function countByCategory(): Collection
    {
        return $this->active()
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();
    }

    /** @return Collection<int, Task> cada item traz `total` e a prioridade carregada */
    public function countByPriority(): Collection
    {
        return $this->active()
            ->selectRaw('priority_id, COUNT(*) as total')
            ->groupBy('priority_id')
            ->with('priority')
            ->orderByDesc('total')
            ->get();
    }
}
