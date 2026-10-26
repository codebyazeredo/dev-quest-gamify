<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Enums\XpSourceType;
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
use Illuminate\Testing\TestResponse;
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

    /** @return array<int, string> tipos dos toasts anexados à resposta JSON da API */
    private function toastTypes(TestResponse $response): array
    {
        return collect($response->json('toasts', []))->pluck('type')->all();
    }

    public function test_leveling_up_via_task_completion_returns_a_level_up_toast(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $category = TaskCategory::factory()->create(['base_points' => 100]);
        $approved = $board->columns->firstWhere('status', TaskStatus::APPROVED);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $approved->id,
            'status' => TaskStatus::APPROVED,
            'assigned_to' => $developer->id,
            'category_id' => $category->id,
            'base_points' => 100,
            'priority_multiplier' => '1.50',
        ]);

        $response = $this->actingAs($developer)->postJson(route('api.tasks.homologation', $task))->assertOk();

        $this->assertContains('level_up', $this->toastTypes($response));
    }

    public function test_unlocking_an_achievement_via_task_completion_returns_an_achievement_toast(): void
    {
        $this->seed(AchievementSeeder::class);
        $this->seed(TitleSeeder::class);

        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug', 'base_points' => 10]);
        $approved = $board->columns->firstWhere('status', TaskStatus::APPROVED);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $approved->id,
            'status' => TaskStatus::APPROVED,
            'assigned_to' => $developer->id,
            'category_id' => $bugCategory->id,
            'base_points' => 10,
            'priority_multiplier' => '1.50',
        ]);

        $response = $this->actingAs($developer)->postJson(route('api.tasks.homologation', $task))->assertOk();

        $this->assertContains('achievement', $this->toastTypes($response));
    }

    public function test_completing_a_challenge_returns_a_challenge_toast(): void
    {
        $this->seed(ChallengeSeeder::class);

        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug']);
        $approved = $board->columns->firstWhere('status', TaskStatus::APPROVED);

        // Evita que um level up domine o resultado: o foco aqui é o toast do desafio.
        app(XpService::class)->grant($developer, 10_000_000, XpSourceType::TASK, null, 'test setup buffer');

        $toastTypes = [];

        for ($i = 0; $i < 10; $i++) {
            $task = Task::factory()->create([
                'board_id' => $board->id,
                'column_id' => $approved->id,
                'status' => TaskStatus::APPROVED,
                'assigned_to' => $developer->id,
                'category_id' => $bugCategory->id,
                'priority_multiplier' => '1.50',
            ]);

            $response = $this->actingAs($developer)->postJson(route('api.tasks.homologation', $task))->assertOk();
            $toastTypes = [...$toastTypes, ...$this->toastTypes($response)];
        }

        $this->assertContains('challenge', $toastTypes);
    }

    public function test_a_fifth_consecutive_checkin_returns_a_streak_toast(): void
    {
        $developer = User::factory()->developer()->create();

        Carbon::setTestNow('2026-01-01');

        for ($i = 0; $i < 4; $i++) {
            $this->actingAs($developer)->postJson(route('api.me.checkin'))->assertOk();
            Carbon::setTestNow(now()->addDay());
        }

        $response = $this->actingAs($developer)->postJson(route('api.me.checkin'))->assertOk();

        $streak = collect($response->json('toasts'))->firstWhere('type', 'streak');

        $this->assertNotNull($streak);
        $this->assertStringContainsString('5 dias consecutivos', $streak['message']);
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
            'base_points' => 100,
            'priority_multiplier' => '1.50',
        ]);

        app(XpService::class)->grant($developer, 90, XpSourceType::TASK, null, 'test setup buffer');

        $response = $this->actingAs($reviewer)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $testing->id, 'position' => 0])
            ->assertOk();

        $this->assertNotContains('level_up', $this->toastTypes($response));
    }

    public function test_api_errors_return_the_specific_message_with_the_matching_status(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->getJson('/api/rota-que-nao-existe')
            ->assertNotFound()
            ->assertJsonPath('message', 'O recurso solicitado não existe.');
    }
}
