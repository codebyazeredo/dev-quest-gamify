<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskDueReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignee_is_reminded_once_when_due_date_is_near(): void
    {
        $assignee = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => $assignee->id, 'due_at' => now()->addHours(5), 'completed_at' => null]);

        $this->artisan('tasks:send-due-reminders')->expectsOutput('1 lembrete(s) enviado(s).')->assertSuccessful();
        $this->artisan('tasks:send-due-reminders')->expectsOutput('0 lembrete(s) enviado(s).')->assertSuccessful();

        $this->assertSame('reminder', $assignee->notifications()->firstOrFail()->data['kind']);
        $this->assertNotNull($task->fresh()->due_reminder_sent_at);
    }

    public function test_far_unassigned_and_completed_tasks_are_ignored(): void
    {
        $assignee = User::factory()->developer()->create();

        Task::factory()->create(['assigned_to' => $assignee->id, 'due_at' => now()->addDays(5), 'completed_at' => null]);
        Task::factory()->create(['assigned_to' => null, 'due_at' => now()->addHour(), 'completed_at' => null]);
        Task::factory()->create(['assigned_to' => $assignee->id, 'due_at' => now()->addHour(), 'completed_at' => now()]);

        $this->artisan('tasks:send-due-reminders')->expectsOutput('0 lembrete(s) enviado(s).')->assertSuccessful();
    }
}
