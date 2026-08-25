<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoCheckinOnLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
    }

    public function test_logging_in_automatically_checks_in_the_user_for_the_day(): void
    {
        $user = User::factory()->developer()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('daily_checkins', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);
        $this->assertSame(1, $user->xpTransactions()->sum('amount'));
    }

    public function test_logging_in_twice_the_same_day_does_not_double_checkin(): void
    {
        $user = User::factory()->developer()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertDatabaseCount('daily_checkins', 1);
        $this->assertSame(1, $user->xpTransactions()->sum('amount'));
    }
}
