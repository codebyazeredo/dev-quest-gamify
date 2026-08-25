<?php

namespace Tests\Feature;

use App\Enums\XpSourceType;
use App\Livewire\Gamification\Ranking;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_ranking(): void
    {
        $response = $this->get(route('ranking'));

        $response->assertRedirect(route('login'));
    }

    public function test_ranking_orders_users_by_total_xp_descending(): void
    {
        $viewer = User::factory()->create();

        $top = User::factory()->create(['name' => 'Top Scorer']);
        $middle = User::factory()->create(['name' => 'Middle Scorer']);
        $bottom = User::factory()->create(['name' => 'Bottom Scorer']);

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
}
