<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskEventType;
use App\Livewire\Admin\EventRules;
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
            ->test(EventRules::class)
            ->call('edit', $rule->id)
            ->set('editingXpReward', 30)
            ->set('editingActive', false)
            ->call('update');

        $rule->refresh();
        $this->assertSame(30, $rule->xp_reward);
        $this->assertFalse($rule->active);
    }

    public function test_developer_is_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/event-rules');

        $response->assertForbidden();
    }
}
