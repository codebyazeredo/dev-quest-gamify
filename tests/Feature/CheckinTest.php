<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
    }

    public function test_a_developer_can_check_in(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get(route('checkin'))->assertViewHas('checkedInToday', false);

        $this->actingAs($developer)->postJson(route('api.me.checkin'))
            ->assertOk()
            ->assertJsonPath('data.checked_in_today', true);

        $this->assertDatabaseHas('daily_checkins', ['user_id' => $developer->id]);
        $this->assertSame(1, $developer->xpTransactions()->sum('amount'));

        $this->actingAs($developer)->get(route('checkin'))->assertViewHas('checkedInToday', true);
    }

    public function test_an_admin_can_also_check_in(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.me.checkin'))
            ->assertOk()
            ->assertJsonPath('data.checked_in_today', true);
    }

    public function test_a_second_check_in_the_same_day_is_a_no_op(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->postJson(route('api.me.checkin'))->assertOk();
        $this->actingAs($developer)->postJson(route('api.me.checkin'))->assertOk();

        $this->assertDatabaseCount('daily_checkins', 1);
        $this->assertSame(1, $developer->xpTransactions()->sum('amount'));
    }

    public function test_checking_in_returns_a_persistent_checkin_toast(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->postJson(route('api.me.checkin'))
            ->assertOk()
            ->assertJsonPath('toasts.0.type', 'checkin');
    }

    public function test_a_repeat_check_in_the_same_day_does_not_return_another_checkin_toast(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->postJson(route('api.me.checkin'))->assertOk();

        $response = $this->actingAs($developer)->postJson(route('api.me.checkin'))->assertOk();

        $this->assertArrayNotHasKey('toasts', $response->json());
    }
}
