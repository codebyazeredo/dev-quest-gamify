<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\TaskRepository;
use App\Repositories\XpTransactionRepository;

/** Dados da página "Minha conta" do usuário logado. */
class UserDashboardService
{
    private const OPEN_TASKS_LIMIT = 6;

    private const RECENT_ACTIVITY_LIMIT = 5;

    public function __construct(
        private LevelService $levels,
        private CheckinService $checkins,
        private AchievementService $achievements,
        private TitleService $titles,
        private TaskRepository $tasks,
        private XpTransactionRepository $xpTransactions,
    ) {}

    /** @return array<string, mixed> */
    public function overviewFor(User $user): array
    {
        $levelProgress = $this->levels->progressFor($user);

        return [
            'levelProgress' => $levelProgress,
            'totalXp' => $levelProgress['totalXp'],
            'tasksCompleted' => $this->tasks->countCompletedAssignedTo($user),
            'xpThisWeek' => $this->xpTransactions->sumForUserSince($user, now()->startOfWeek()),
            'rankingPosition' => $this->xpTransactions->countUsersWithTotalAbove($levelProgress['totalXp']) + 1,
            'achievementsCount' => $this->achievements->countUnlockedFor($user),
            'titlesCount' => $this->titles->countUnlockedFor($user),
            'currentStreak' => $this->checkins->currentStreakFor($user),
            'checkedInToday' => $this->checkins->hasCheckedInToday($user),
            'openTasks' => $this->tasks->openAssignedTo($user, self::OPEN_TASKS_LIMIT),
            'recentActivity' => $this->xpTransactions->latestFor($user, self::RECENT_ACTIVITY_LIMIT),
            'selectedTitle' => $user->selectedTitle,
        ];
    }
}
