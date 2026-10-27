<?php

namespace Tests\Feature\Admin;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'nome' => 'Maria Teste',
            'cpf' => '52998224725',
            'nascimento' => '1990-01-01',
            'sexo' => 2,
            'email' => 'maria@example.test',
            'telefone1' => '11999998888',
            'cep' => '01310-100',
            'logradouro' => 'Av. Paulista',
            'numero' => '1000',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            ...$overrides,
        ];
    }

    public function test_admin_can_create_a_person_with_address(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.people.store'), $this->payload())->assertCreated();

        $person = Person::where('email', 'maria@example.test')->firstOrFail();

        $this->assertSame('Maria Teste', $person->nome);
        $this->assertSame('52998224725', $person->cpf);
        $this->assertNotNull($person->address);
        $this->assertSame('São Paulo', $person->address->cidade);
    }

    public function test_invalid_cpf_checksum_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.people.store'), $this->payload(['cpf' => '11111111111']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cpf');

        $this->assertNull(Person::where('email', 'maria@example.test')->first());
    }

    public function test_duplicate_cpf_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Person::factory()->create(['cpf' => '52998224725']);

        $this->actingAs($admin)
            ->postJson(route('api.people.store'), $this->payload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cpf');
    }

    public function test_admin_can_update_a_person(): void
    {
        $admin = User::factory()->admin()->create();
        $person = Person::factory()->withAddress()->create(['nome' => 'Old Name']);

        $this->actingAs($admin)
            ->putJson(route('api.people.update', $person), $this->payload(['nome' => 'New Name', 'cpf' => $person->cpf, 'email' => $person->email]))
            ->assertOk();

        $this->assertSame('New Name', $person->refresh()->nome);
    }

    public function test_cannot_delete_a_person_that_already_has_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $linked = User::factory()->developer()->create();

        $this->actingAs($admin)->deleteJson(route('api.people.destroy', $linked->person_id))->assertStatus(409);

        $this->assertNotNull(Person::find($linked->person_id));
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/people')->assertForbidden();
        $this->actingAs($developer)->postJson(route('api.people.store'), $this->payload())->assertForbidden();
    }
}
