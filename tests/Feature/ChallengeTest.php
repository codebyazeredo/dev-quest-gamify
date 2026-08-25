<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\TaskService;
use Database\Seeders\ChallengeSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
        $this->seed(ChallengeSeeder::class);
    }

    protected function completeTask(User $developer, Board $board, ?TaskCategory $category = null): void
    {
        $doing = $board->columns->firstWhere('status', TaskStatus::DOING);
        $done = $board->columns->firstWhere('status', TaskStatus::DONE);

        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $doing->id,
            'status' => TaskStatus::DOING,
            'assigned_to' => $developer->id,
            'category_id' => $category?->id ?? TaskCategory::factory()->create()->id,
        ]);

        app(TaskService::class)->move($task, $done, 0, $developer);
    }

    public function test_completing_tasks_progresses_desafio_da_semana(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);

        $this->completeTask($developer, $board);
        $this->completeTask($developer, $board);

        $userChallenge = UserChallenge::where('user_id', $developer->id)
            ->whereHas('challenge', fn ($q) => $q->where('slug', 'desafio-da-semana'))
            ->first();

        $this->assertNotNull($userChallenge);
        $this->assertSame(2, $userChallenge->progress);
        $this->assertNull($userChallenge->completed_at);
    }

    public function test_completing_bug_week_target_grants_xp_once(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $bugCategory = TaskCategory::factory()->create(['slug' => 'bug']);

        for ($i = 0; $i < 10; $i++) {
            $this->completeTask($developer, $board, $bugCategory);
        }

        $userChallenge = UserChallenge::where('user_id', $developer->id)
            ->whereHas('challenge', fn ($q) => $q->where('slug', 'bug-week'))
            ->first();

        $this->assertNotNull($userChallenge);
        $this->assertSame(10, $userChallenge->progress);
        $this->assertNotNull($userChallenge->completed_at);
        $this->assertSame(200, $developer->xpTransactions()->where('description', 'like', '%Bug Week%')->sum('amount'));
    }
}
