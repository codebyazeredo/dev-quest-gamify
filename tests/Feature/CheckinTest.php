<?php

namespace Tests\Feature;

use App\Livewire\Checkin\Button;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        Livewire::actingAs($developer)
            ->test(Button::class)
            ->assertViewHas('checkedInToday', false)
            ->call('checkIn')
            ->assertViewHas('checkedInToday', true);

        $this->assertDatabaseHas('daily_checkins', ['user_id' => $developer->id]);
        $this->assertSame(1, $developer->xpTransactions()->sum('amount'));
    }

    public function test_an_admin_can_also_check_in(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Button::class)
            ->call('checkIn')
            ->assertViewHas('checkedInToday', true);
    }

    public function test_a_second_check_in_the_same_day_is_a_no_op(): void
    {
        $developer = User::factory()->developer()->create();

        $component = Livewire::actingAs($developer)->test(Button::class);
        $component->call('checkIn');
        $component->call('checkIn');

        $this->assertDatabaseCount('daily_checkins', 1);
        $this->assertSame(1, $developer->xpTransactions()->sum('amount'));
    }
}
