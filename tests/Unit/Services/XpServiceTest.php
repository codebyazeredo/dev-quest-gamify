<?php

namespace Tests\Unit\Services;

use App\Enums\XpSourceType;
use App\Events\LevelUp;
use App\Models\Level;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\XpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class XpServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        Level::factory()->create(['level' => 2, 'xp_required' => 100]);
    }

    public function test_grant_creates_and_returns_a_transaction(): void
    {
        $user = User::factory()->create();

        $transaction = app(XpService::class)->grant($user, 50, XpSourceType::BONUS, null, 'Test bonus');

        $this->assertInstanceOf(XpTransaction::class, $transaction);
        $this->assertDatabaseHas('xp_transactions', [
            'user_id' => $user->id,
            'amount' => 50,
            'description' => 'Test bonus',
        ]);
    }

    public function test_grant_returns_null_and_creates_nothing_for_zero_amount(): void
    {
        $user = User::factory()->create();

        $transaction = app(XpService::class)->grant($user, 0, XpSourceType::BONUS, null, 'No-op');

        $this->assertNull($transaction);
        $this->assertDatabaseCount('xp_transactions', 0);
    }

    public function test_grant_dispatches_level_up_only_when_a_threshold_is_crossed(): void
    {
        Event::fake();
        $user = User::factory()->create();

        app(XpService::class)->grant($user, 50, XpSourceType::BONUS, null, 'Below threshold');
        Event::assertNotDispatched(LevelUp::class);

        app(XpService::class)->grant($user, 60, XpSourceType::BONUS, null, 'Crosses threshold');
        Event::assertDispatched(LevelUp::class, function (LevelUp $event) use ($user) {
            return $event->user->is($user)
                && $event->previousLevel->level === 1
                && $event->newLevel->level === 2;
        });
    }
}
