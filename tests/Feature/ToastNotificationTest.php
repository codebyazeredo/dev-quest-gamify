<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
use App\Livewire\Checkin\Button;
use App\Livewire\Task\Kanban;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Services\XpService;
use App\Support\ToastCollector;
use Database\Seeders\AchievementSeeder;
use Database\Seeders\ChallengeSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Database\Seeders\TitleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class ToastNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_leveling_up_via_task_completion_dispatches_a_level_up_toast(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $category = TaskCategory::factory()->create(['base_points' => 100]);

        // task already past Review: the assignee moving Testing -> Done is not a
        // self-review sign-off, so the developer can trigger their own toast —
        // and since the toast is now scoped to auth()->id() === $event->user->id
        // (bugfix), the developer must be both the assignee AND the actor here
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $testing->id,
            'status' => TaskStatus::TESTING,
            'assigned_to' => $developer->id,
            'category_id' => $category->id,
            'priority' => TaskPriority::NORMAL,
            'base_points' => 100,
            'priority_multiplier' => TaskPriority::NORMAL->multiplier(),
        ]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $done->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'level_up');
    }

    public function test_unlocking_an_achievement_via_task_completion_dispatches_an_achievement_toast(): void
    {
        $this->seed(AchievementSeeder::class);
        $this->seed(TitleSeeder::class);

        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug', 'base_points' => 10]);

        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $testing->id,
            'status' => TaskStatus::TESTING,
            'assigned_to' => $developer->id,
            'category_id' => $bugCategory->id,
            // fixed low XP so the task completion + achievement unlock never also
            // cross a level threshold — assertDispatched only inspects the first
            // "toast"-named dispatch, so a level_up would mask the achievement toast
            'priority' => TaskPriority::NORMAL,
            'base_points' => 10,
            'priority_multiplier' => TaskPriority::NORMAL->multiplier(),
        ]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $done->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'achievement');
    }

    public function test_completing_a_challenge_dispatches_a_challenge_toast(): void
    {
        $this->seed(ChallengeSeeder::class);

        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug']);

        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        // pre-level the developer to the max seeded level so the challenge's own XP
        // reward can never also cross a level threshold and queue a level_up toast
        // ahead of the challenge toast this test asserts on (assertDispatched only
        // inspects the first "toast"-named dispatch, so any level_up would mask it)
        app(XpService::class)->grant($developer, 10_000_000, XpSourceType::TASK, null, 'test setup buffer');

        $component = Livewire::actingAs($developer)->test(Kanban::class, ['board' => $board]);

        for ($i = 0; $i < 10; $i++) {
            $task = Task::factory()->create([
                'board_id' => $board->id,
                'column_id' => $testing->id,
                'status' => TaskStatus::TESTING,
                'assigned_to' => $developer->id,
                'category_id' => $bugCategory->id,
                'priority' => TaskPriority::NORMAL,
                'priority_multiplier' => TaskPriority::NORMAL->multiplier(),
            ]);

            $component->call('moveTask', $task->id, $done->id, 0);
        }

        $component->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'challenge');
    }

    public function test_a_fifth_consecutive_checkin_dispatches_a_streak_toast(): void
    {
        $developer = User::factory()->developer()->create();

        Carbon::setTestNow('2026-01-01');

        for ($i = 0; $i < 4; $i++) {
            Livewire::actingAs($developer)->test(Button::class)->call('checkIn');
            Carbon::setTestNow(now()->addDay());
        }

        Livewire::actingAs($developer)
            ->test(Button::class)
            ->call('checkIn')
            ->assertDispatched('toast', function ($name, $params) {
                return $params['toast']['type'] === 'streak'
                    && str_contains($params['toast']['message'], '5 dias consecutivos');
            });
    }

    public function test_flush_returns_empty_when_nothing_was_queued(): void
    {
        $this->assertSame([], app(ToastCollector::class)->flush());
    }

    public function test_an_actor_does_not_see_a_toast_about_someone_elses_level_up(): void
    {
        $developer = User::factory()->developer()->create();
        $reviewer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $category = TaskCategory::factory()->create(['base_points' => 100]);

        $review = $board->columns->firstWhere('status', TaskStatus::REVIEW);
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $review->id,
            'status' => TaskStatus::REVIEW,
            'assigned_to' => $developer->id,
            'category_id' => $category->id,
            'priority' => TaskPriority::NORMAL,
            'base_points' => 100,
            'priority_multiplier' => TaskPriority::NORMAL->multiplier(),
        ]);

        // 90 + the 10 XP the REVIEW_COMPLETED threshold grants on this move = 100,
        // exactly crossing into level 2
        app(XpService::class)->grant($developer, 90, XpSourceType::TASK, null, 'test setup buffer');

        // the reviewer's own move crosses the developer's level threshold, but
        // the reviewer is not the subject of that LevelUp event — no toast for them
        Livewire::actingAs($reviewer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $testing->id, 0)
            ->assertNotDispatched('toast');
    }
}
