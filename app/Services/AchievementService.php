<?php

namespace App\Services;

use App\Enums\AchievementConditionType;
use App\Enums\XpSourceType;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;
use App\Repositories\AchievementRepository;
use App\Repositories\DailyCheckinRepository;
use App\Repositories\TaskEventRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserAchievementRepository;
use App\Repositories\UserChallengeRepository;
use App\Repositories\UserTitleRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    public function __construct(
        private XpService $xpService,
        private AchievementRepository $achievements,
        private UserAchievementRepository $userAchievements,
        private UserTitleRepository $userTitles,
        private UserChallengeRepository $userChallenges,
        private TaskRepository $tasks,
        private TaskEventRepository $taskEvents,
        private DailyCheckinRepository $checkins,
    ) {}

    /**
     * Conquistas ativas com o progresso do usuário; administradores enxergam todas como desbloqueadas.
     *
     * @return Collection<int, array{achievement: Achievement, unlocked: bool, progress: int}>
     */
    public function catalogFor(User $user): Collection
    {
        $unlockedIds = $this->userAchievements->unlockedIdsFor($user);
        $progressByType = [];

        return $this->achievements->allActive()->map(function (Achievement $achievement) use ($user, $unlockedIds, &$progressByType) {
            $progressByType[$achievement->condition_type->value] ??= $this->valueFor($user, $achievement->condition_type);

            return [
                'achievement' => $achievement,
                'unlocked' => $user->isAdmin() || in_array($achievement->id, $unlockedIds, true),
                'progress' => $progressByType[$achievement->condition_type->value],
            ];
        });
    }

    public function countUnlockedFor(User $user): int
    {
        return $user->isAdmin() ? $this->achievements->countActive() : $this->userAchievements->countFor($user);
    }

    public function evaluateForUser(User $user): void
    {
        $unlockedIds = $this->userAchievements->unlockedIdsFor($user);
        $cache = [];

        $this->achievements->activeExcluding($unlockedIds)
            ->each(function (Achievement $achievement) use ($user, &$cache) {
                $cache[$achievement->condition_type->value] ??= $this->valueFor($user, $achievement->condition_type);

                if ($cache[$achievement->condition_type->value] >= $achievement->condition_value) {
                    $this->unlock($user, $achievement);
                }
            });
    }

    public function valueFor(User $user, AchievementConditionType $type): int
    {
        return match ($type) {
            AchievementConditionType::BUGS_RESOLVED => $this->tasks->countCompletedBugsAssignedTo($user),
            AchievementConditionType::DEPLOYS_MADE => $this->taskEvents->countDeploysOfTasksAssignedTo($user),
            AchievementConditionType::TASKS_COMPLETED_IN_A_DAY => $this->tasks->maxCompletedInOneDayBy($user),
            AchievementConditionType::TASKS_COMPLETED_TOTAL => $this->tasks->countCompletedAssignedTo($user),
            AchievementConditionType::CHALLENGES_COMPLETED => $this->userChallenges->countCompletedBy($user),
            AchievementConditionType::CHECKINS_TOTAL => $this->checkins->countFor($user),
        };
    }

    protected function unlock(User $user, Achievement $achievement): void
    {
        if ($this->userAchievements->isUnlocked($user, $achievement->id)) {
            return;
        }

        DB::transaction(function () use ($user, $achievement) {
            $this->userAchievements->unlock($user, $achievement->id);

            if ($achievement->xp_reward > 0) {
                $this->xpService->grant(
                    $user,
                    $achievement->xp_reward,
                    XpSourceType::ACHIEVEMENT,
                    $achievement->id,
                    "Conquista desbloqueada - {$achievement->name}",
                );
            }

            $title = $achievement->title;

            if ($title !== null) {
                $this->userTitles->grant($user->id, $title->id);
            }

            event(new AchievementUnlocked($user, $achievement));
        });
    }
}
