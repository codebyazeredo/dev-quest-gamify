<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Livewire\Task\Kanban;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignee_cannot_advance_their_own_task_out_of_review_self_review(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $review = $board->columns->firstWhere('status', TaskStatus::REVIEW);
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $review->id,
            'status' => TaskStatus::REVIEW,
            'assigned_to' => $developer->id,
        ]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $testing->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertSame($review->id, $task->refresh()->column_id);
    }

    public function test_a_different_developer_can_advance_the_task_out_of_review(): void
    {
        $assignee = User::factory()->developer()->create();
        $reviewer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $review = $board->columns->firstWhere('status', TaskStatus::REVIEW);
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $review->id,
            'status' => TaskStatus::REVIEW,
            'assigned_to' => $assignee->id,
        ]);

        Livewire::actingAs($reviewer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $testing->id, 0);

        $this->assertSame($testing->id, $task->refresh()->column_id);
    }

    public function test_assignee_can_still_submit_their_own_task_for_review(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $doing = $board->columns->firstWhere('status', TaskStatus::DOING);
        $review = $board->columns->firstWhere('status', TaskStatus::REVIEW);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $doing->id,
            'status' => TaskStatus::DOING,
            'assigned_to' => $developer->id,
        ]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $review->id, 0);

        $this->assertSame($review->id, $task->refresh()->column_id);
    }

    public function test_admin_and_product_owner_can_advance_a_task_out_of_review_regardless_of_assignment(): void
    {
        $assignee = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $review = $board->columns->firstWhere('status', TaskStatus::REVIEW);
        $testing = $board->columns->firstWhere('status', TaskStatus::TESTING);

        foreach ([User::factory()->admin()->create(), User::factory()->productOwner()->create()] as $actor) {
            $task = Task::factory()->create([
                'board_id' => $board->id,
                'column_id' => $review->id,
                'status' => TaskStatus::REVIEW,
                'assigned_to' => $assignee->id,
            ]);

            Livewire::actingAs($actor)
                ->test(Kanban::class, ['board' => $board])
                ->call('moveTask', $task->id, $testing->id, 0);

            $this->assertSame($testing->id, $task->refresh()->column_id);
        }
    }

    public function test_a_different_developer_cannot_perform_a_non_sign_off_move_on_someone_elses_task(): void
    {
        $assignee = User::factory()->developer()->create();
        $otherDeveloper = User::factory()->developer()->create();
        $board = Board::factory()->create();
        BoardColumn::seedDefaultsFor($board);
        $todo = $board->columns->firstWhere('status', TaskStatus::TODO);
        $doing = $board->columns->firstWhere('status', TaskStatus::DOING);
        $task = Task::factory()->create([
            'board_id' => $board->id,
            'column_id' => $todo->id,
            'status' => TaskStatus::TODO,
            'assigned_to' => $assignee->id,
        ]);

        Livewire::actingAs($otherDeveloper)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $doing->id, 0)
            ->assertDispatched('toast', fn ($name, $params) => $params['toast']['type'] === 'error');

        $this->assertSame($todo->id, $task->refresh()->column_id);
    }
}
