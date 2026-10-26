<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskEventType;
use App\Models\TaskEventRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRulesManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_rule(): void
    {
        $admin = User::factory()->admin()->create();
        $rule = TaskEventRule::factory()->create(['type' => TaskEventType::DEPLOYED, 'xp_reward' => 20, 'active' => true]);

        $this->actingAs($admin)
            ->putJson(route('api.event-rules.update', TaskEventType::DEPLOYED->value), ['xp_reward' => 30, 'active' => false])
            ->assertOk();

        $rule->refresh();

        $this->assertSame(30, $rule->xp_reward);
        $this->assertFalse($rule->active);
    }

    public function test_admin_can_create_a_rule_for_an_unconfigured_event_type(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseMissing('task_event_rules', ['type' => TaskEventType::APPROVED]);

        $this->actingAs($admin)
            ->postJson(route('api.event-rules.store'), ['type' => TaskEventType::APPROVED->value, 'xp_reward' => 15, 'active' => true])
            ->assertCreated();

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

        $this->actingAs($admin)
            ->postJson(route('api.event-rules.store'), ['type' => TaskEventType::DEPLOYED->value, 'xp_reward' => 10, 'active' => true])
            ->assertStatus(409);

        $this->assertSame(1, TaskEventRule::where('type', TaskEventType::DEPLOYED)->count());
    }

    public function test_percentage_based_events_are_limited_to_one_hundred(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.event-rules.store'), ['type' => TaskEventType::APPROVED->value, 'xp_reward' => 150, 'active' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('xp_reward');
    }

    public function test_index_lists_every_event_type(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson(route('api.event-rules.index'))
            ->assertOk()
            ->assertJsonCount(count(TaskEventType::cases()), 'data');
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/event-rules')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.event-rules.index'))->assertForbidden();
    }
}
