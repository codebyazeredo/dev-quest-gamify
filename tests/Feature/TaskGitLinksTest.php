<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskGitLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_and_pull_request_are_saved_and_returned_for_editing(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->untagged()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id]);

        $this->actingAs($admin)
            ->putJson(route('api.tasks.update', $task), [
                'title' => $task->title,
                'category_id' => $task->category_id,
                'priority_id' => $task->priority_id,
                'branch_name' => 'feature/dq-12-portal',
                'pull_request_url' => 'https://github.com/acme/app/pull/12',
            ])
            ->assertOk()
            ->assertJsonPath('data.branch_name', 'feature/dq-12-portal')
            ->assertJsonPath('data.pull_request_url', 'https://github.com/acme/app/pull/12');

        $this->actingAs($admin)
            ->get(route('boards.show', $board))
            ->assertOk()
            ->assertSee('feature/dq-12-portal')
            ->assertSee('https://github.com/acme/app/pull/12', false);
    }

    public function test_git_links_can_be_cleared(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['branch_name' => 'feature/x', 'pull_request_url' => 'https://github.com/acme/app/pull/1']);

        $this->actingAs($admin)
            ->putJson(route('api.tasks.update', $task), [
                'title' => $task->title,
                'category_id' => $task->category_id,
                'priority_id' => $task->priority_id,
                'branch_name' => '',
                'pull_request_url' => '',
            ])
            ->assertOk();

        $task->refresh();

        $this->assertNull($task->branch_name);
        $this->assertNull($task->pull_request_url);
    }

    public function test_invalid_branch_and_non_http_links_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $column = BoardColumn::factory()->for(Board::factory()->create())->untagged()->create();

        $this->actingAs($admin)
            ->postJson(route('api.tasks.store'), [
                'column_id' => $column->id,
                'title' => 'Com git',
                'category_id' => TaskCategory::factory()->create()->id,
                'priority_id' => TaskPriority::factory()->create()->id,
                'branch_name' => 'nome com espaço',
                'pull_request_url' => 'javascript:alert(1)',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['branch_name', 'pull_request_url']);
    }
}
