<?php

namespace Tests\Feature\Auth;

use App\Models\AppSetting;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_login_screen_shows_the_animated_panel_and_the_portal_link(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('quest-panel', false)
            ->assertSee('data-theme-toggle', false)
            ->assertSee('Desenvolvido por', false)
            ->assertSee('Planeje, entregue e reconheça resultados.')
            ->assertSee(route('portal.login'), false);
    }

    public function test_login_logo_height_follows_the_configured_size(): void
    {
        foreach (['small' => 4, 'medium' => 6, 'large' => 8] as $size => $rem) {
            AppSetting::current()->update(['logo_size' => $size, 'logo_path' => 'logos/empresa.png']);

            $this->get('/login')->assertOk()->assertSee("--quest-logo-h: {$rem}rem", false);
        }
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/boards');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
