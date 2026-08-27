<?php

namespace Tests\Feature;

use App\Enums\XpSourceType;
use App\Livewire\Gamification\Ranking;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_guest_cannot_view_ranking(): void
    {
        $response = $this->get(route('ranking'));

        $response->assertRedirect(route('login'));
    }

    public function test_ranking_orders_users_by_total_xp_descending(): void
    {
        $viewer = User::factory()->create();

        $top = User::factory()->developer()->create(['name' => 'Top Scorer']);
        $middle = User::factory()->developer()->create(['name' => 'Middle Scorer']);
        $bottom = User::factory()->developer()->create(['name' => 'Bottom Scorer']);

        XpTransaction::factory()->create(['user_id' => $top->id, 'amount' => 100, 'source_type' => XpSourceType::BONUS]);
        XpTransaction::factory()->create(['user_id' => $middle->id, 'amount' => 50, 'source_type' => XpSourceType::BONUS]);
        XpTransaction::factory()->create(['user_id' => $bottom->id, 'amount' => 10, 'source_type' => XpSourceType::BONUS]);

        $names = Livewire::actingAs($viewer)
            ->test(Ranking::class)
            ->viewData('users')
            ->pluck('name')
            ->values()
            ->all();

        $position = array_flip($names);

        $this->assertTrue($position['Top Scorer'] < $position['Middle Scorer']);
        $this->assertTrue($position['Middle Scorer'] < $position['Bottom Scorer']);
    }

    public function test_dev_and_tester_rankings_are_separate_and_exclude_other_roles(): void
    {
        $viewer = User::factory()->create();

        $developer = User::factory()->developer()->create(['name' => 'Dev Person']);
        $tester = User::factory()->tester()->create(['name' => 'Tester Person']);
        $admin = User::factory()->admin()->create(['name' => 'Admin Person']);

        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 50, 'source_type' => XpSourceType::BONUS]);
        XpTransaction::factory()->create(['user_id' => $tester->id, 'amount' => 50, 'source_type' => XpSourceType::BONUS]);
        XpTransaction::factory()->create(['user_id' => $admin->id, 'amount' => 50, 'source_type' => XpSourceType::BONUS]);

        $devNames = Livewire::actingAs($viewer)->test(Ranking::class)->viewData('users')->pluck('name')->values()->all();
        $this->assertContains('Dev Person', $devNames);
        $this->assertNotContains('Tester Person', $devNames);
        $this->assertNotContains('Admin Person', $devNames);

        $testerNames = Livewire::actingAs($viewer)->test(Ranking::class)->call('setRole', 'tester')->viewData('users')->pluck('name')->values()->all();
        $this->assertContains('Tester Person', $testerNames);
        $this->assertNotContains('Dev Person', $testerNames);
        $this->assertNotContains('Admin Person', $testerNames);
    }

    public function test_weekly_ranking_only_counts_xp_earned_this_week(): void
    {
        Carbon::setTestNow('2026-08-27 12:00:00');

        $viewer = User::factory()->create();
        $developer = User::factory()->developer()->create(['name' => 'Dev Person']);

        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 40, 'source_type' => XpSourceType::BONUS, 'created_at' => now()]);
        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 100, 'source_type' => XpSourceType::BONUS, 'created_at' => now()->subWeeks(2)]);

        $weekly = Livewire::actingAs($viewer)
            ->test(Ranking::class)
            ->call('setPeriod', 'week')
            ->viewData('users')
            ->firstWhere('name', 'Dev Person');

        $this->assertSame(40, (int) $weekly->total_xp);
    }

    public function test_monthly_ranking_only_counts_xp_earned_this_month(): void
    {
        Carbon::setTestNow('2026-08-27 12:00:00');

        $viewer = User::factory()->create();
        $developer = User::factory()->developer()->create(['name' => 'Dev Person']);

        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 40, 'source_type' => XpSourceType::BONUS, 'created_at' => now()]);
        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 100, 'source_type' => XpSourceType::BONUS, 'created_at' => now()->subMonths(2)]);

        $monthly = Livewire::actingAs($viewer)
            ->test(Ranking::class)
            ->call('setPeriod', 'month')
            ->viewData('users')
            ->firstWhere('name', 'Dev Person');

        $this->assertSame(40, (int) $monthly->total_xp);
    }

    public function test_total_ranking_counts_all_time_xp(): void
    {
        Carbon::setTestNow('2026-08-27 12:00:00');

        $viewer = User::factory()->create();
        $developer = User::factory()->developer()->create(['name' => 'Dev Person']);

        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 40, 'source_type' => XpSourceType::BONUS, 'created_at' => now()]);
        XpTransaction::factory()->create(['user_id' => $developer->id, 'amount' => 100, 'source_type' => XpSourceType::BONUS, 'created_at' => now()->subYear()]);

        $total = Livewire::actingAs($viewer)
            ->test(Ranking::class)
            ->viewData('users')
            ->firstWhere('name', 'Dev Person');

        $this->assertSame(140, (int) $total->total_xp);
    }
}
