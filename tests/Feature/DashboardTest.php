<?php

namespace Tests\Feature;

use App\Enums\XpSourceType;
use App\Livewire\Dashboard\Index;
use App\Models\Level;
use App\Models\Title;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_correct_level_xp_and_ranking_position(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        Level::factory()->create(['level' => 2, 'xp_required' => 100]);

        $ahead = User::factory()->create();
        XpTransaction::factory()->create(['user_id' => $ahead->id, 'amount' => 500, 'source_type' => XpSourceType::BONUS]);

        $user = User::factory()->create();
        XpTransaction::factory()->create(['user_id' => $user->id, 'amount' => 150, 'source_type' => XpSourceType::BONUS]);

        $component = Livewire::actingAs($user)->test(Index::class);

        $component->assertViewHas('totalXp', 150);
        $component->assertViewHas('currentLevel', fn ($level) => $level->level === 2);
        $component->assertViewHas('rankingPosition', 2);
    }

    public function test_navbar_shows_the_users_active_title(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        $title = Title::factory()->create(['name' => 'Code Warrior']);
        $user = User::factory()->create(['selected_title_id' => $title->id]);

        $this->actingAs($user)->get('/dashboard')->assertSee('Code Warrior');
    }

    public function test_navbar_falls_back_to_role_label_without_an_active_title(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
        $user = User::factory()->developer()->create();

        $this->actingAs($user)->get('/dashboard')->assertSee('Desenvolvedor');
    }
}
