<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskChecklistTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_adds_an_item_to_the_checklist(): void
    {
        $task = Task::factory()->create();

        $this->actingAs(User::factory()->developer()->create())
            ->postJson(route('api.tasks.checklist.store', $task), ['title' => 'Escrever testes'])
            ->assertCreated()
            ->assertJsonPath('message', 'Item adicionado ao checklist.');

        $this->assertDatabaseHas('task_checklist_items', ['task_id' => $task->id, 'title' => 'Escrever testes', 'completed_at' => null]);
    }

    public function test_item_title_is_validated(): void
    {
        $url = route('api.tasks.checklist.store', Task::factory()->create());
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->postJson($url, ['title' => ''])->assertUnprocessable()->assertJsonValidationErrors('title');
        $this->actingAs($developer)->postJson($url, ['title' => str_repeat('a', 151)])->assertUnprocessable()->assertJsonValidationErrors('title');
    }

    public function test_toggle_completes_records_who_and_reopens(): void
    {
        $developer = User::factory()->developer()->create();
        $item = TaskChecklistItem::factory()->create();

        $this->actingAs($developer)->postJson(route('api.checklist.toggle', $item))->assertOk()->assertJsonPath('message', 'Item concluído.');

        $item->refresh();
        $this->assertNotNull($item->completed_at);
        $this->assertSame($developer->id, $item->completed_by);

        $this->actingAs($developer)->postJson(route('api.checklist.toggle', $item))->assertOk()->assertJsonPath('message', 'Item reaberto.');

        $item->refresh();
        $this->assertNull($item->completed_at);
        $this->assertNull($item->completed_by);
    }

    public function test_item_is_removed(): void
    {
        $item = TaskChecklistItem::factory()->create();

        $this->actingAs(User::factory()->developer()->create())
            ->deleteJson(route('api.checklist.destroy', $item))
            ->assertOk();

        $this->assertModelMissing($item);
    }

    public function test_archived_tasks_block_checklist_changes(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['archived_at' => now()]);
        $item = TaskChecklistItem::factory()->for($task)->create();

        $this->actingAs($developer)->postJson(route('api.tasks.checklist.store', $task), ['title' => 'Novo item'])->assertForbidden();
        $this->actingAs($developer)->postJson(route('api.checklist.toggle', $item))->assertForbidden();
        $this->actingAs($developer)->deleteJson(route('api.checklist.destroy', $item))->assertForbidden();
    }

    public function test_progress_is_shown_on_the_task_page(): void
    {
        $task = Task::factory()->create();
        TaskChecklistItem::factory()->for($task)->create(['title' => 'Item feito', 'completed_at' => now()]);
        TaskChecklistItem::factory()->for($task)->create(['title' => 'Item pendente']);

        $this->actingAs(User::factory()->developer()->create())
            ->get(route('tasks.show', $task))
            ->assertOk()
            ->assertSee('Item feito')
            ->assertSee('Item pendente')
            ->assertSee('1/2');
    }
}
