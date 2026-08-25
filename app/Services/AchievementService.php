<?php

namespace App\Services;

use App\Enums\AchievementConditionType;
use App\Enums\TaskEventType;
use App\Enums\XpSourceType;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserTitle;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    public function __construct(private XpService $xpService) {}

    public function evaluateForUser(User $user): void
    {
        $unlockedIds = UserAchievement::where('user_id', $user->id)->pluck('achievement_id');

        Achievement::where('active', true)
            ->whereNotIn('id', $unlockedIds)
            ->get()
            ->each(function (Achievement $achievement) use ($user) {
                if ($this->valueFor($user, $achievement->condition_type) >= $achievement->condition_value) {
                    $this->unlock($user, $achievement);
                }
            });
    }

    public function valueFor(User $user, AchievementConditionType $type): int
    {
        return match ($type) {
            AchievementConditionType::BUGS_RESOLVED => Task::where('assigned_to', $user->id)
                ->whereNotNull('completed_at')
                ->whereHas('category', fn ($query) => $query->where('slug', 'bug'))
                ->count(),

            AchievementConditionType::DEPLOYS_MADE => TaskEvent::where('type', TaskEventType::DEPLOYED)
                ->whereHas('task', fn ($query) => $query->where('assigned_to', $user->id))
                ->count(),

            AchievementConditionType::TASKS_COMPLETED_IN_A_DAY => (int) Task::where('assigned_to', $user->id)
                ->whereNotNull('completed_at')
                ->selectRaw('COUNT(*) as c')
                ->groupBy(DB::raw('DATE(completed_at)'))
                ->get()
                ->max('c'),

            AchievementConditionType::TASKS_COMPLETED_TOTAL => Task::where('assigned_to', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
        };
    }

    protected function unlock(User $user, Achievement $achievement): void
    {
        if (UserAchievement::where('user_id', $user->id)->where('achievement_id', $achievement->id)->exists()) {
            return;
        }

        DB::transaction(function () use ($user, $achievement) {
            UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'unlocked_at' => now(),
            ]);

            if ($achievement->xp_reward > 0) {
                $this->xpService->grant(
                    $user,
                    $achievement->xp_reward,
                    XpSourceType::ACHIEVEMENT,
                    $achievement->id,
                    "Achievement unlocked - {$achievement->name}",
                );
            }

            $title = $achievement->title;

            if ($title !== null) {
                UserTitle::firstOrCreate(
                    ['user_id' => $user->id, 'title_id' => $title->id],
                    ['unlocked_at' => now()]
                );
            }

            event(new AchievementUnlocked($user, $achievement));
        });
    }
}
