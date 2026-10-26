<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
