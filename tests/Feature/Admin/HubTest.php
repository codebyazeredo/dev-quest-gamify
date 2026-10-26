<?php

namespace Tests\Feature\Admin;

use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_hub_with_every_section(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Gestão')
            ->assertSee('Gamificação')
            ->assertSee('Sistema')
            ->assertSee('Usuários')
            ->assertSee('Categorias')
            ->assertSee('Configurações');
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin')->assertForbidden();
    }

    public function test_the_navbar_dropdown_only_shows_settings_to_an_admin(): void
    {
        Level::factory()->create(['level' => 1, 'xp_required' => 0]);

        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();

        $this->actingAs($admin)->get(route('dashboard'))->assertSee('Configurações');
        $this->actingAs($developer)->get(route('dashboard'))->assertDontSee('Configurações');
    }
}
