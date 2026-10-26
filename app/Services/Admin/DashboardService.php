<?php

namespace App\Services\Admin;

use App\Enums\TaskStatus;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Repositories\XpTransactionRepository;

class DashboardService
{
    private const CHART_DAYS = 7;

    public function __construct(
        private TaskRepository $tasks,
        private XpTransactionRepository $xp,
        private UserRepository $users,
    ) {}

    public function overview(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();

        return [
            'indicators' => [
                'developers' => $this->users->countWithRole('dev'),
                'openTasks' => $this->tasks->countOpen(),
                'lateTasks' => $this->tasks->countLate(),
                'unassignedTasks' => $this->tasks->countUnassigned(),
                'completedToday' => $this->tasks->countCompletedSince($today),
                'completedThisWeek' => $this->tasks->countCompletedSince($weekStart),
                'xpToday' => $this->xp->sumSince($today),
                'xpThisWeek' => $this->xp->sumSince($weekStart),
                'activeUsers' => $this->xp->countActiveUsersSince($weekStart),
            ],
            'byStatus' => $this->statusBreakdown(),
            'byCategory' => $this->tasks->countByCategory(),
            'byPriority' => $this->tasks->countByPriority(),
            'xpPerDay' => $this->xpPerDay(),
            'topUsers' => $this->xp->topUsersSince($weekStart, 5),
        ];
    }

    /** @return array<int, array{label: string, total: int}> */
    private function statusBreakdown(): array
    {
        $totals = $this->tasks->countByStatus();

        return array_map(
            fn (TaskStatus $status) => ['label' => $status->label(), 'total' => $totals[$status->value] ?? 0],
            TaskStatus::cases(),
        );
    }

    /** @return array<int, array{label: string, total: int}> últimos dias, incluindo os sem XP */
    private function xpPerDay(): array
    {
        $since = now()->subDays(self::CHART_DAYS - 1)->startOfDay();
        $totals = $this->xp->sumPerDaySince($since);

        return collect(range(0, self::CHART_DAYS - 1))
            ->map(function (int $offset) use ($since, $totals) {
                $day = $since->addDays($offset);

                return ['label' => $day->format('d/m'), 'total' => $totals->get($day->toDateString(), 0)];
            })
            ->all();
    }
}
