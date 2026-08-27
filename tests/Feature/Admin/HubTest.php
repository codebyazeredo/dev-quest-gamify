<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Hub;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HubTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_hub_with_every_section(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Hub::class)
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

        $response = $this->actingAs($developer)->get('/admin');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Hub::class)
            ->assertForbidden();
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
