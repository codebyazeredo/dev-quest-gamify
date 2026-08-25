<?php

namespace Tests\Feature;

use App\Enums\TaskEventType;
use App\Livewire\Task\Show;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\User;
use Database\Seeders\LevelSeeder;
use Database\Seeders\TaskEventRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManualEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
        $this->seed(TaskEventRuleSeeder::class);
    }

    public function test_assigned_developer_can_mark_homologation_and_deploy(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => $developer->id]);

        Livewire::actingAs($developer)
            ->test(Show::class, ['task' => $task])
            ->call('markHomologationCompleted')
            ->call('markDeployed');

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::HOMOLOGATION_COMPLETED->value]);
        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::DEPLOYED->value]);
    }

    public function test_admin_and_product_owner_can_mark_homologation_and_deploy(): void
    {
        $admin = User::factory()->admin()->create();
        $po = User::factory()->productOwner()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        Livewire::actingAs($admin)->test(Show::class, ['task' => $task])->call('markHomologationCompleted');
        Livewire::actingAs($po)->test(Show::class, ['task' => $task])->call('markDeployed');

        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::HOMOLOGATION_COMPLETED->value]);
        $this->assertDatabaseHas('task_events', ['task_id' => $task->id, 'type' => TaskEventType::DEPLOYED->value]);
    }

    public function test_unrelated_developer_is_forbidden(): void
    {
        $assignee = User::factory()->developer()->create();
        $other = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => $assignee->id]);

        Livewire::actingAs($other)
            ->test(Show::class, ['task' => $task])
            ->call('markHomologationCompleted')
            ->assertForbidden();
    }

    public function test_repeat_calls_are_idempotent(): void
    {
        $developer = User::factory()->developer()->create();
        $task = Task::factory()->create(['assigned_to' => $developer->id]);

        $component = Livewire::actingAs($developer)->test(Show::class, ['task' => $task]);
        $component->call('markDeployed');
        $component->call('markDeployed');

        $this->assertSame(
            1,
            TaskEvent::where('task_id', $task->id)->where('type', TaskEventType::DEPLOYED->value)->count()
        );
    }
}
