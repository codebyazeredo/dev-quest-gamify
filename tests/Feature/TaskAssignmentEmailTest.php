<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskAssignmentEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignee_receives_an_email_when_an_admin_assigns_the_task(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.assign', $task), ['user_id' => $developer->id])
            ->assertOk();

        Notification::assertSentTo($developer, TaskAssignedMail::class);
    }

    public function test_no_email_is_sent_when_the_actor_assigns_to_themselves(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->actingAs($admin)
            ->postJson(route('api.tasks.assign', $task), ['user_id' => $admin->id])
            ->assertOk();

        Notification::assertNothingSentTo($admin);
    }

    public function test_email_content_has_task_title_assigner_and_link(): void
    {
        $actor = User::factory()->admin()->create(['name' => 'Ana Admin']);
        $task = Task::factory()->create(['title' => 'Corrigir login', 'due_at' => null]);

        $mail = (new TaskAssignedMail($task, $actor))->toMail(User::factory()->developer()->create(['name' => 'Beto']));

        $this->assertStringContainsString('Corrigir login', $mail->subject);
        $this->assertSame('Abrir tarefa', $mail->actionText);
        $this->assertSame(route('tasks.show', $task), $mail->actionUrl);
        $this->assertContains('Ana Admin atribuiu a tarefa "Corrigir login" a você.', $mail->introLines);
    }
}
