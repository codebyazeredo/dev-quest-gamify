<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskEventType;
use App\Livewire\Admin\EventRules\Create;
use App\Livewire\Admin\EventRules\Edit;
use App\Livewire\Admin\EventRules\Index;
use App\Models\TaskEventRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventRulesManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_rule(): void
    {
        $admin = User::factory()->admin()->create();
        $rule = TaskEventRule::factory()->create(['type' => TaskEventType::DEPLOYED, 'xp_reward' => 20, 'active' => true]);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['typeValue' => TaskEventType::DEPLOYED->value])
            ->set('xp_reward', 30)
            ->set('active', false)
            ->call('save');

        $rule->refresh();
        $this->assertSame(30, $rule->xp_reward);
        $this->assertFalse($rule->active);
    }

    public function test_admin_can_create_a_rule_for_an_unconfigured_event_type(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseMissing('task_event_rules', ['type' => TaskEventType::APPROVED]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('type', TaskEventType::APPROVED->value)
            ->set('xp_reward', 15)
            ->set('active', true)
            ->call('save');

        $this->assertDatabaseHas('task_event_rules', [
            'type' => TaskEventType::APPROVED->value,
            'xp_reward' => 15,
            'active' => true,
        ]);
    }

    public function test_cannot_create_a_second_rule_for_an_already_configured_type(): void
    {
        $admin = User::factory()->admin()->create();
        TaskEventRule::factory()->create(['type' => TaskEventType::DEPLOYED]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('type', TaskEventType::DEPLOYED->value)
            ->set('xp_reward', 10)
            ->call('save')
            ->assertHasErrors(['type']);

        $this->assertSame(1, TaskEventRule::where('type', TaskEventType::DEPLOYED)->count());
    }

    public function test_developer_is_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/event-rules');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Index::class)
            ->assertForbidden();
    }
}
