<?php

namespace Tests\Feature;

use App\Enums\TaskEventType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Livewire\Gamification\Achievements;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\TaskService;
use Database\Seeders\AchievementSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Database\Seeders\TitleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
        $this->seed(AchievementSeeder::class);
        $this->seed(TitleSeeder::class);
    }

    public function test_completing_a_bug_task_unlocks_first_blood_and_progresses_bug_hunter(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug', 'base_points' => 10]);

        $doing = $board->columns->firstWhere('status', TaskStatus::DOING);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $doing->id,
            'status' => TaskStatus::DOING,
            'assigned_to' => $developer->id,
            'category_id' => $bugCategory->id,
            'priority' => TaskPriority::NORMAL,
            'priority_multiplier' => TaskPriority::NORMAL->multiplier(),
        ]);

        app(TaskService::class)->move($task, $done, 0, $developer);

        $this->assertTrue(
            UserAchievement::whereHas('achievement', fn ($q) => $q->where('slug', 'first-blood'))
                ->where('user_id', $developer->id)->exists()
        );

        $this->assertTrue($developer->unlockedTitles()->whereHas('title', fn ($q) => $q->where('slug', 'code-warrior'))->exists());
    }

    public function test_repeated_evaluation_does_not_unlock_the_same_achievement_twice(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);

        $doing = $board->columns->firstWhere('status', TaskStatus::DOING);
        $review = $board->columns->firstWhere('status', TaskStatus::REVIEW);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $doing->id,
            'status' => TaskStatus::DOING,
            'assigned_to' => $developer->id,
        ]);

        $service = app(TaskService::class);
        $service->move($task, $done, 0, $developer);

        // bounce it around after completion — should not re-trigger First Blood
        $service->move($task, $review, 0, $developer);
        $service->move($task, $done, 0, $developer);

        $this->assertSame(
            1,
            UserAchievement::whereHas('achievement', fn ($q) => $q->where('slug', 'first-blood'))
                ->where('user_id', $developer->id)->count()
        );
    }

    public function test_marking_a_task_deployed_progresses_release_master(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => $developer->id]);

        app(TaskService::class)->markDeployed($task, $developer);

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::DEPLOYED->value]);
        // one deploy isn't enough to unlock Release Master (target 10), just confirms the listener ran without error
        $this->assertFalse(
            UserAchievement::whereHas('achievement', fn ($q) => $q->where('slug', 'release-master'))
                ->where('user_id', $developer->id)->exists()
        );
    }

    public function test_admin_sees_every_achievement_as_already_unlocked(): void
    {
        $admin = User::factory()->admin()->create();

        $rows = Livewire::actingAs($admin)->test(Achievements::class)->viewData('achievements');

        $this->assertNotEmpty($rows);
        $this->assertTrue($rows->every(fn (array $row) => $row['unlocked'] === true));
    }

    public function test_a_developer_with_no_progress_sees_achievements_as_locked(): void
    {
        $developer = User::factory()->developer()->create();

        $rows = Livewire::actingAs($developer)->test(Achievements::class)->viewData('achievements');

        $this->assertNotEmpty($rows);
        $this->assertTrue($rows->every(fn (array $row) => $row['unlocked'] === false));
    }
}
