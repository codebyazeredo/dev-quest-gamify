<?php

namespace Tests\Feature;

use App\Enums\PullRequestState;
use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GithubWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'segredo-de-teste';

    private const PULL_REQUEST_URL = 'https://github.com/acme/app/pull/7';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.github.webhook_secret' => self::SECRET]);
    }

    public function test_opened_pull_request_moves_task_to_review_and_stores_state(): void
    {
        [$board, $task] = $this->linkedTask(TaskStatus::DOING);
        $reviewColumn = BoardColumn::factory()->for($board)->status(TaskStatus::REVIEW)->create();

        $this->sendPullRequest('opened', ['state' => 'open', 'merged' => false])->assertOk();

        $task->refresh();

        $this->assertSame(PullRequestState::OPEN, $task->pull_request_state);
        $this->assertSame($reviewColumn->id, $task->column_id);
    }

    public function test_merged_pull_request_moves_task_to_testing(): void
    {
        [$board, $task] = $this->linkedTask(TaskStatus::REVIEW);
        $testingColumn = BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create();

        $this->sendPullRequest('closed', ['state' => 'closed', 'merged' => true])->assertOk();

        $task->refresh();

        $this->assertSame(PullRequestState::MERGED, $task->pull_request_state);
        $this->assertSame($testingColumn->id, $task->column_id);
    }

    public function test_closed_without_merge_only_updates_state(): void
    {
        [, $task] = $this->linkedTask(TaskStatus::DOING);
        $columnId = $task->column_id;

        $this->sendPullRequest('closed', ['state' => 'closed', 'merged' => false])->assertOk();

        $task->refresh();

        $this->assertSame(PullRequestState::CLOSED, $task->pull_request_state);
        $this->assertSame($columnId, $task->column_id);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $this->linkedTask(TaskStatus::DOING);

        $this->postJson(route('webhooks.github'), $this->payload('opened', []), [
            'X-GitHub-Event' => 'pull_request',
            'X-Hub-Signature-256' => 'sha256=invalida',
        ])->assertUnauthorized();
    }

    public function test_missing_secret_returns_service_unavailable(): void
    {
        config(['services.github.webhook_secret' => null]);

        $this->postJson(route('webhooks.github'), [])->assertStatus(503);
    }

    public function test_unlinked_repository_is_acknowledged_without_changes(): void
    {
        $this->sendPullRequest('opened', ['state' => 'open', 'merged' => false])
            ->assertOk()
            ->assertJsonPath('message', 'Nenhum quadro está vinculado ao repositório acme/app.');
    }

    /** @return array{0: Board, 1: Task} */
    private function linkedTask(TaskStatus $status): array
    {
        $board = Board::factory()->create(['github_repository' => 'acme/app']);
        $column = BoardColumn::factory()->for($board)->status($status)->create();
        $developer = User::factory()->create();

        $task = Task::factory()->assignedTo($developer)->create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'status' => $status,
            'pull_request_url' => self::PULL_REQUEST_URL,
        ]);

        return [$board, $task];
    }

    private function sendPullRequest(string $action, array $pullRequest)
    {
        $body = json_encode($this->payload($action, $pullRequest));

        return $this->call('POST', route('webhooks.github'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_GITHUB_EVENT' => 'pull_request',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $body, self::SECRET),
        ], $body);
    }

    private function payload(string $action, array $pullRequest): array
    {
        return [
            'action' => $action,
            'repository' => ['full_name' => 'acme/app'],
            'pull_request' => [
                ...$pullRequest,
                'html_url' => self::PULL_REQUEST_URL,
                'head' => ['ref' => 'feature/dq-7'],
            ],
        ];
    }
}
