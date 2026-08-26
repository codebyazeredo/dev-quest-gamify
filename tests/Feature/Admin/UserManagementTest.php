<?php

namespace Tests\Feature\Admin;

use App\Enums\XpSourceType;
use App\Livewire\Admin\Users;
use App\Models\Person;
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
        $person = Person::factory()->create(['nome' => 'Nova Pessoa']);

        $component = Livewire::actingAs($admin)
            ->test(Users::class)
            ->set('personId', $person->id)
            ->set('email', 'nova@devquestgamify.test')
            ->set('password', 'super-secret-1')
            ->set('password_confirmation', 'super-secret-1')
            ->set('roles', ['dev'])
            ->call('create');

        $user = User::where('email', 'nova@devquestgamify.test')->first();
        $this->assertNotNull($user);
        $this->assertSame($person->id, $user->person_id);
        $this->assertTrue($user->isDeveloper());
        $this->assertTrue(Hash::check('super-secret-1', $user->password));

        $component->call('edit', $user->id)
            ->set('editingEmail', 'atualizada@devquestgamify.test')
            ->set('editingRoles', ['product_owner'])
            ->call('update');

        $user->refresh();
        $this->assertSame('atualizada@devquestgamify.test', $user->email);
        $this->assertTrue($user->isProductOwner());
        $this->assertFalse($user->isDeveloper());

        $component->call('delete', $user->id);
        $this->assertNull(User::find($user->id));
    }

    public function test_cannot_create_a_user_for_a_person_that_already_has_one(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->developer()->create();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->set('personId', $existing->person_id)
            ->set('email', 'duplicado@devquestgamify.test')
            ->set('password', 'super-secret-1')
            ->set('password_confirmation', 'super-secret-1')
            ->set('roles', ['dev'])
            ->call('create')
            ->assertHasErrors(['personId']);

        $this->assertNull(User::where('email', 'duplicado@devquestgamify.test')->first());
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
