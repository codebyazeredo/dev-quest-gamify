<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\XpSourceType;
use App\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_user(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(Users::class)
            ->set('name', 'Nova Pessoa')
            ->set('email', 'nova@devquestgamify.test')
            ->set('password', 'super-secret-1')
            ->set('password_confirmation', 'super-secret-1')
            ->set('role', UserRole::DEVELOPER->value)
            ->call('create');

        $user = User::where('email', 'nova@devquestgamify.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isDeveloper());
        $this->assertTrue(Hash::check('super-secret-1', $user->password));

        $component->call('edit', $user->id)
            ->set('editingName', 'Pessoa Atualizada')
            ->set('editingRole', UserRole::PRODUCT_OWNER->value)
            ->call('update');

        $user->refresh();
        $this->assertSame('Pessoa Atualizada', $user->name);
        $this->assertTrue($user->isProductOwner());

        $component->call('delete', $user->id);
        $this->assertNull(User::find($user->id));
    }

    public function test_cannot_create_a_user_with_the_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->set('name', 'Tentando Ser Admin')
            ->set('email', 'tentando@devquestgamify.test')
            ->set('password', 'super-secret-1')
            ->set('password_confirmation', 'super-secret-1')
            ->set('role', UserRole::ADMIN->value)
            ->call('create')
            ->assertHasErrors(['role']);

        $this->assertNull(User::where('email', 'tentando@devquestgamify.test')->first());
    }

    public function test_cannot_delete_a_user_with_xp_history(): void
    {
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $developer->xpTransactions()->create([
            'amount' => 10,
            'source_type' => XpSourceType::CHECKIN,
            'description' => 'Daily check-in',
        ]);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('delete', $developer->id);

        $this->assertNotNull(User::find($developer->id));
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/users');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Users::class)
            ->assertForbidden();
    }
}
