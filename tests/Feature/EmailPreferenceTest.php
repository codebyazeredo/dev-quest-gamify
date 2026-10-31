<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\User;
use App\Notifications\TaskAssignedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailPreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_emails_are_enabled_and_two_factor_is_off_by_default(): void
    {
        $user = User::factory()->developer()->create()->fresh();

        $this->assertTrue($user->email_notifications);
        $this->assertFalse($user->hasTwoFactorEnabled());
    }

    public function test_user_turns_off_assignment_emails(): void
    {
        $user = User::factory()->developer()->create();

        $this->actingAs($user)
            ->put(route('preferences.email'), ['email_notifications' => 0])
            ->assertRedirect(route('security'));

        $this->assertFalse($user->fresh()->email_notifications);
    }

    public function test_mail_channel_is_skipped_when_the_user_opted_out(): void
    {
        $task = Task::factory()->create();
        $notification = new TaskAssignedMail($task, User::factory()->admin()->create());

        $this->assertSame(['mail'], $notification->via(User::factory()->developer()->create(['email_notifications' => true])));
        $this->assertSame([], $notification->via(User::factory()->developer()->create(['email_notifications' => false])));
    }

    public function test_kanban_card_shows_checklist_progress(): void
    {
        $board = Board::factory()->create();
        $column = BoardColumn::factory()->for($board)->create();
        $task = Task::factory()->create(['board_id' => $board->id, 'column_id' => $column->id]);
        TaskChecklistItem::factory()->for($task)->create(['completed_at' => now()]);
        TaskChecklistItem::factory()->for($task)->create();
        TaskChecklistItem::factory()->for($task)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('boards.show', $board))
            ->assertOk()
            ->assertSee('1/3');
    }
}
