<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_comments_on_a_task_and_assignee_is_notified(): void
    {
        $author = User::factory()->tester()->create();
        $assignee = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => $assignee->id]);

        $this->actingAs($author)
            ->postJson(route('api.tasks.comments.store', $task), ['body' => 'Falta validar o campo CPF.'])
            ->assertCreated()
            ->assertJsonPath('message', 'Comentário adicionado.');

        $this->assertDatabaseHas('task_comments', ['task_id' => $task->id, 'user_id' => $author->id]);
        $this->assertSame('commented', $assignee->notifications()->firstOrFail()->data['kind']);
        $this->assertCount(0, $author->notifications);
    }

    public function test_empty_and_oversized_comments_are_rejected(): void
    {
        $user = User::factory()->developer()->create();
        $url = route('api.tasks.comments.store', Task::factory()->create());

        $this->actingAs($user)->postJson($url, ['body' => ''])->assertUnprocessable()->assertJsonValidationErrors('body');
        $this->actingAs($user)->postJson($url, ['body' => str_repeat('a', 2001)])->assertUnprocessable()->assertJsonValidationErrors('body');
    }

    public function test_archived_tasks_do_not_accept_comments(): void
    {
        $user = User::factory()->developer()->create();
        $task = Task::factory()->create(['archived_at' => now()]);

        $this->actingAs($user)
            ->postJson(route('api.tasks.comments.store', $task), ['body' => 'Olá'])
            ->assertForbidden();
    }

    public function test_only_author_or_admin_deletes_a_comment(): void
    {
        $author = User::factory()->developer()->create();
        $other = User::factory()->developer()->create();
        $admin = User::factory()->admin()->create();
        $comment = TaskComment::factory()->create(['user_id' => $author->id]);

        $this->actingAs($other)->deleteJson(route('api.comments.destroy', $comment))->assertForbidden();
        $this->actingAs($author)->deleteJson(route('api.comments.destroy', $comment))->assertOk();

        $secondComment = TaskComment::factory()->create(['user_id' => $author->id]);
        $this->actingAs($admin)->deleteJson(route('api.comments.destroy', $secondComment))->assertOk();

        $this->assertSame(0, TaskComment::count());
    }

    public function test_comments_are_listed_on_the_task_page(): void
    {
        $user = User::factory()->developer()->create();
        $comment = TaskComment::factory()->create(['body' => 'Comentário visível na página']);

        $this->actingAs($user)
            ->get(route('tasks.show', $comment->task))
            ->assertOk()
            ->assertSee('Comentário visível na página');
    }
}
