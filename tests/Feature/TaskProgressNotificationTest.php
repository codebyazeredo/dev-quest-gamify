<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $doing->id, 'position' => 0])
            ->assertOk();

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

        $this->actingAs($developer)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $doing->id, 'position' => 0])
            ->assertOk();

        $this->assertCount(0, $developer->notifications);
    }

    public function test_testers_are_notified_when_a_task_reaches_testing(): void
    {
        [$board, , $doing, $testing] = $this->boardWithColumns();
        $admin = User::factory()->admin()->create();
        $tester = User::factory()->tester()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $doing->id, 'status' => TaskStatus::DOING, 'assigned_to' => User::factory()->developer()->create()->id]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $testing->id, 'position' => 0])
            ->assertOk();

        $this->assertSame('testing', $tester->notifications()->first()->data['kind']);
    }

    public function test_bell_lists_only_the_users_notifications_and_marks_them_as_read(): void
    {
        [$board, $todo, $doing] = $this->boardWithColumns();
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $other = User::factory()->developer()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id, 'title' => 'Tarefa do andamento']);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $doing->id, 'position' => 0])
            ->assertOk();

        $this->actingAs($other)->getJson(route('api.notifications.index'))
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('unread_count', 0);

        $notificationId = $developer->notifications()->first()->id;

        $this->actingAs($developer)->getJson(route('api.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Tarefa do andamento')
            ->assertJsonPath('data.0.task_id', $task->id)
            ->assertJsonPath('unread_count', 1);

        $this->actingAs($other)->postJson(route('api.notifications.read', $notificationId))->assertNotFound();

        $this->actingAs($developer)->postJson(route('api.notifications.read', $notificationId))->assertOk();

        $this->assertSame(0, $developer->unreadNotifications()->count());
    }

    public function test_marking_all_notifications_as_read_clears_the_unread_count(): void
    {
        [$board, $todo, $doing] = $this->boardWithColumns();
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $todo->id, 'status' => TaskStatus::TODO, 'assigned_to' => $developer->id]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.move', $task), ['column_id' => $doing->id, 'position' => 0])
            ->assertOk();

        $this->actingAs($developer)->postJson(route('api.notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertSame(0, $developer->unreadNotifications()->count());
    }

    public function test_guests_cannot_read_notifications(): void
    {
        $this->getJson(route('api.notifications.index'))
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Sessão expirada. Faça login novamente.');
    }
}
