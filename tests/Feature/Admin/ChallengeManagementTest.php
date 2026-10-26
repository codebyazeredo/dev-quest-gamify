<?php

namespace Tests\Feature\Admin;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Sprint Push',
            'icon' => 'flag',
            'type' => ChallengeType::TASKS_COMPLETED->value,
            'target' => 3,
            'xp_reward' => 40,
            'starts_at' => now()->startOfWeek()->format('Y-m-d\TH:i'),
            'ends_at' => now()->endOfWeek()->format('Y-m-d\TH:i'),
            ...$overrides,
        ];
    }

    public function test_admin_can_create_update_and_delete_a_challenge(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.challenges.store'), $this->payload())->assertCreated();

        $challenge = Challenge::where('name', 'Sprint Push')->firstOrFail();

        $this->actingAs($admin)
            ->putJson(route('api.challenges.update', $challenge), $this->payload(['name' => 'Sprint Push II', 'active' => true]))
            ->assertOk();

        $this->assertSame('Sprint Push II', $challenge->refresh()->name);

        $this->actingAs($admin)->deleteJson(route('api.challenges.destroy', $challenge))->assertOk();

        $this->assertNull(Challenge::find($challenge->id));
    }

    public function test_the_end_must_be_after_the_start(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.challenges.store'), $this->payload(['ends_at' => now()->startOfWeek()->subDay()->format('Y-m-d\TH:i')]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ends_at');
    }

    public function test_admin_can_toggle_and_duplicate_a_challenge(): void
    {
        $admin = User::factory()->admin()->create();
        $challenge = Challenge::factory()->create(['active' => true]);

        $this->actingAs($admin)->postJson(route('api.challenges.toggle-active', $challenge))->assertOk();
        $this->assertFalse($challenge->refresh()->active);

        $this->actingAs($admin)->postJson(route('api.challenges.duplicate', $challenge))->assertCreated();
        $this->assertSame(2, Challenge::count());
    }

    public function test_index_paginates_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        Challenge::factory()->count(20)->create();

        $this->actingAs($admin)->getJson(route('api.challenges.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_developer_gets_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/challenges')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.challenges.index'))->assertForbidden();
    }
}
