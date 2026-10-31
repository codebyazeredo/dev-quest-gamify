<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Models\Level;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Support\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Level::factory()->create(['level' => 1, 'xp_required' => 0]);
    }

    private function userWithTwoFactor(): User
    {
        $user = User::factory()->admin()->create(['password' => 'secret-pass']);
        $user->forceFill([
            'two_factor_secret' => Totp::generateSecret(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['abcde-12345', 'fghij-67890'],
        ])->save();

        return $user->fresh();
    }

    private function validCode(User $user): string
    {
        return Totp::code($user->two_factor_secret, Totp::currentStep());
    }

    public function test_totp_matches_the_rfc_6238_test_vector(): void
    {
        $this->assertSame('287082', Totp::code('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', 1));
        $this->assertSame(32, strlen(Totp::generateSecret()));
    }

    public function test_user_enables_two_factor_and_receives_recovery_codes(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->post(route('security.two-factor.enable'))->assertRedirect(route('security'));
        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertFalse($user->hasTwoFactorEnabled());

        $this->actingAs($user)
            ->post(route('security.two-factor.confirm'), ['code' => $this->validCode($user)])
            ->assertRedirect(route('security'))
            ->assertSessionHas('recovery_codes', fn (array $codes) => count($codes) === 8);

        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_wrong_code_does_not_enable_two_factor(): void
    {
        $user = User::factory()->admin()->create();
        app(TwoFactorService::class)->beginSetup($user);

        $this->actingAs($user)
            ->post(route('security.two-factor.confirm'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_login_with_two_factor_requires_the_code_before_authenticating(): void
    {
        $user = $this->userWithTwoFactor();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('two-factor.challenge'));

        $this->assertGuest();
        $this->assertDatabaseCount('daily_checkins', 0);

        $this->post(route('two-factor.challenge'), ['code' => $this->validCode($user)])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_code_keeps_the_user_logged_out(): void
    {
        $user = $this->userWithTwoFactor();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass']);
        $this->from(route('two-factor.challenge'))->post(route('two-factor.challenge'), ['code' => '111111'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_a_used_code_cannot_be_replayed(): void
    {
        $user = $this->userWithTwoFactor();
        $code = $this->validCode($user);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass']);
        $this->post(route('two-factor.challenge'), ['code' => $code])->assertRedirect();

        $this->post(route('logout'));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass']);
        $this->post(route('two-factor.challenge'), ['code' => $code])->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_recovery_code_works_once(): void
    {
        $user = $this->userWithTwoFactor();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass']);
        $this->post(route('two-factor.challenge'), ['code' => 'abcde-12345'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(['fghij-67890'], $user->fresh()->two_factor_recovery_codes);

        $this->post(route('logout'));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass']);
        $this->post(route('two-factor.challenge'), ['code' => 'abcde-12345'])->assertSessionHasErrors('code');
    }

    public function test_challenge_without_pending_login_or_expired_goes_back_to_login(): void
    {
        $this->get(route('two-factor.challenge'))->assertRedirect(route('login'));

        $user = $this->userWithTwoFactor();
        $this->withSession([TwoFactorChallengeController::SESSION_KEY => ['user_id' => $user->id, 'remember' => false, 'expires_at' => now()->subMinute()->timestamp]])
            ->post(route('two-factor.challenge'), ['code' => $this->validCode($user)])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_disabling_requires_the_current_password(): void
    {
        $user = $this->userWithTwoFactor();

        $this->actingAs($user)->delete(route('security.two-factor.disable'), ['password' => 'wrong'])->assertSessionHasErrors('password');
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());

        $this->actingAs($user)->delete(route('security.two-factor.disable'), ['password' => 'secret-pass'])->assertRedirect(route('security'));
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_without_two_factor_still_logs_in_directly(): void
    {
        $user = User::factory()->developer()->create(['password' => 'secret-pass']);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-pass'])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }
}
