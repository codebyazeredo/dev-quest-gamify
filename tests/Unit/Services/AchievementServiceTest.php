<?php

namespace Tests\Unit\Services;

use App\Enums\AchievementConditionType;
use App\Enums\TaskEventType;
use App\Models\Achievement;
use App\Models\Level;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskEvent;
use App\Models\Title;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserTitle;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
    }

    public function test_value_for_bugs_resolved_counts_completed_bug_tasks_assigned_to_user(): void
    {
        $user = User::factory()->create();
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug']);
        $otherCategory = TaskCategory::factory()->create(['slug' => 'feature']);

        Task::factory()->count(2)->create(['assigned_to' => $user->id, 'category_id' => $bugCategory->id, 'completed_at' => now()]);
        Task::factory()->create(['assigned_to' => $user->id, 'category_id' => $bugCategory->id, 'completed_at' => null]);
        Task::factory()->create(['assigned_to' => $user->id, 'category_id' => $otherCategory->id, 'completed_at' => now()]);

        $value = app(AchievementService::class)->valueFor($user, AchievementConditionType::BUGS_RESOLVED);

        $this->assertSame(2, $value);
    }

    public function test_value_for_deploys_made_counts_deployed_task_events_for_users_tasks(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $user->id]);
        $otherUserTask = Task::factory()->create(['assigned_to' => User::factory()->create()->id]);

        TaskEvent::factory()->create(['task_id' => $task->id, 'type' => TaskEventType::DEPLOYED]);
        TaskEvent::factory()->create(['task_id' => $task->id, 'type' => TaskEventType::STARTED]);
        TaskEvent::factory()->create(['task_id' => $otherUserTask->id, 'type' => TaskEventType::DEPLOYED]);

        $value = app(AchievementService::class)->valueFor($user, AchievementConditionType::DEPLOYS_MADE);

        $this->assertSame(1, $value);
    }

    public function test_value_for_tasks_completed_in_a_day_returns_the_busiest_day(): void
    {
        $user = User::factory()->create();

        Task::factory()->count(3)->create(['assigned_to' => $user->id, 'completed_at' => now()]);
        Task::factory()->create(['assigned_to' => $user->id, 'completed_at' => now()->subDays(2)]);

        $value = app(AchievementService::class)->valueFor($user, AchievementConditionType::TASKS_COMPLETED_IN_A_DAY);

        $this->assertSame(3, $value);
    }

    public function test_value_for_tasks_completed_total_counts_all_completed_tasks(): void
    {
        $user = User::factory()->create();

        Task::factory()->count(4)->create(['assigned_to' => $user->id, 'completed_at' => now()]);
        Task::factory()->create(['assigned_to' => $user->id, 'completed_at' => null]);

        $value = app(AchievementService::class)->valueFor($user, AchievementConditionType::TASKS_COMPLETED_TOTAL);

        $this->assertSame(4, $value);
    }

    public function test_evaluate_for_user_unlocks_and_grants_xp_and_title_exactly_once(): void
    {
        $user = User::factory()->create();
        $achievement = Achievement::factory()->create([
            'condition_type' => AchievementConditionType::TASKS_COMPLETED_TOTAL,
            'condition_value' => 1,
            'xp_reward' => 50,
        ]);
        $title = Title::factory()->create(['achievement_id' => $achievement->id]);

        Task::factory()->create(['assigned_to' => $user->id, 'completed_at' => now()]);

        $service = app(AchievementService::class);
        $service->evaluateForUser($user);
        $service->evaluateForUser($user);

        $this->assertSame(1, UserAchievement::where('user_id', $user->id)->where('achievement_id', $achievement->id)->count());
        $this->assertSame(1, UserTitle::where('user_id', $user->id)->where('title_id', $title->id)->count());
        $this->assertSame(50, $user->xpTransactions()->sum('amount'));
    }
}
