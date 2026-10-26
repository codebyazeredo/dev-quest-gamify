<?php

namespace Tests\Feature\Admin;

use App\Enums\XpSourceType;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function creationPayload(int $personId, array $overrides = []): array
    {
        return [
            'person_id' => $personId,
            'email' => 'nova@devquestgamify.test',
            'password' => 'super-secret-1',
            'password_confirmation' => 'super-secret-1',
            'roles' => ['dev'],
            ...$overrides,
        ];
    }

    public function test_admin_can_create_update_and_delete_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $person = Person::factory()->create(['nome' => 'Nova Pessoa']);

        $this->actingAs($admin)->postJson(route('api.users.store'), $this->creationPayload($person->id))->assertCreated();

        $user = User::where('email', 'nova@devquestgamify.test')->firstOrFail();

        $this->assertSame($person->id, $user->person_id);
        $this->assertTrue($user->isDeveloper());
        $this->assertTrue(Hash::check('super-secret-1', $user->password));

        $this->actingAs($admin)
            ->putJson(route('api.users.update', $user), ['email' => 'atualizada@devquestgamify.test', 'roles' => ['product_owner']])
            ->assertOk();

        $user->refresh();

        $this->assertSame('atualizada@devquestgamify.test', $user->email);
        $this->assertTrue($user->isProductOwner());
        $this->assertFalse($user->isDeveloper());
        $this->assertTrue(Hash::check('super-secret-1', $user->password));

        $this->actingAs($admin)->deleteJson(route('api.users.destroy', $user))->assertOk();

        $this->assertNull(User::find($user->id));
    }

    public function test_cannot_create_a_user_for_a_person_that_already_has_one(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->developer()->create();

        $this->actingAs($admin)
            ->postJson(route('api.users.store'), $this->creationPayload($existing->person_id, ['email' => 'duplicado@devquestgamify.test']))
            ->assertStatus(409);

        $this->assertNull(User::where('email', 'duplicado@devquestgamify.test')->first());
    }

    public function test_password_is_required_to_create_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $person = Person::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('api.users.store'), $this->creationPayload($person->id, ['password' => '', 'password_confirmation' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
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

        $this->actingAs($admin)->deleteJson(route('api.users.destroy', $developer))->assertStatus(409);

        $this->assertNotNull(User::find($developer->id));
    }

    public function test_index_paginates_the_list_and_offers_form_options(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->developer()->count(20)->create();

        $this->actingAs($admin)->getJson(route('api.users.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonStructure(['form_options' => ['people', 'roles']]);
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/users')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.users.index'))->assertForbidden();
    }
}
