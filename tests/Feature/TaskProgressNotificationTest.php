<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Livewire\Notifications\Bell;
use App\Livewire\Task\Kanban;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskProgressNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function boardWithColumns(): array
    {
        $board = Board::factory()->create();

        return [
            $board,
            BoardColumn::factory()->for($board)->status(TaskStatus::TODO)->create(),
            BoardColumn::factory()->for($board)->status(TaskStatus::DOING)->create(),
            BoardColumn::factory()->for($board)->status(TaskStatus::TESTING)->create(),
        ];
    }

    public function test_assignee_is_notified_when_someone_else_moves_the_task(): void
    {
        [$board, $todo, $doing] = $this->boardWithColumns();
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $doing->id, 0);

        $notification = $developer->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame($task->id, $notification->data['task_id']);
        $this->assertSame('moved', $notification->data['kind']);
        $this->assertCount(0, $admin->notifications);
    }

    public function test_actor_is_never_notified_about_their_own_move(): void
    {
        [$board, $todo, $doing] = $this->boardWithColumns();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id]);

        Livewire::actingAs($developer)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $doing->id, 0);

        $this->assertCount(0, $developer->notifications);
    }

    public function test_testers_are_notified_when_a_task_reaches_testing(): void
    {
        [$board, , $doing, $testing] = $this->boardWithColumns();
        $admin = User::factory()->admin()->create();
        $tester = User::factory()->tester()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $doing->id, 'status' => TaskStatus::DOING, 'assigned_to' => User::factory()->developer()->create()->id]);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $testing->id, 0);

        $this->assertSame('testing', $tester->notifications()->first()->data['kind']);
    }

    public function test_bell_lists_only_the_users_notifications_and_marks_them_as_read(): void
    {
        [$board, $todo, $doing] = $this->boardWithColumns();
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $other = User::factory()->developer()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id, 'title' => 'Tarefa do andamento']);

        Livewire::actingAs($admin)
            ->test(Kanban::class, ['board' => $board])
            ->call('moveTask', $task->id, $doing->id, 0);

        Livewire::actingAs($other)->test(Bell::class)->assertDontSee('Tarefa do andamento');

        $notificationId = $developer->notifications()->first()->id;

        Livewire::actingAs($developer)
            ->test(Bell::class)
            ->assertSee('Tarefa do andamento')
            ->call('openNotification', $notificationId)
            ->assertRedirect(route('tasks.show', $task));

        $this->assertSame(0, $developer->unreadNotifications()->count());
    }
}
