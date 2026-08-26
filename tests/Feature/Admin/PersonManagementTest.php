<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\People\Create;
use App\Livewire\Admin\People\Edit;
use App\Livewire\Admin\People\Index;
use App\Models\Address;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersonManagementTest extends TestCase
{
    use RefreshDatabase;

    private function fillPersonForm(mixed $component): mixed
    {
        return $component
            ->set('nome', 'Maria Teste')
            ->set('cpf', '52998224725')
            ->set('nascimento', '1990-01-01')
            ->set('sexo', 2)
            ->set('email', 'maria@example.test')
            ->set('telefone1', '11999998888')
            ->set('cep', '01310-100')
            ->set('logradouro', 'Av. Paulista')
            ->set('numero', '1000')
            ->set('cidade', 'São Paulo')
            ->set('estado', 'SP');
    }

    public function test_admin_can_create_a_person_with_address(): void
    {
        $admin = User::factory()->admin()->create();

        $this->fillPersonForm(Livewire::actingAs($admin)->test(Create::class))->call('save');

        $person = Person::where('email', 'maria@example.test')->first();
        $this->assertNotNull($person);
        $this->assertSame('Maria Teste', $person->nome);
        $this->assertSame('52998224725', $person->cpf);
        $this->assertNotNull($person->address);
        $this->assertSame('São Paulo', $person->address->cidade);
    }

    public function test_invalid_cpf_checksum_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->fillPersonForm(Livewire::actingAs($admin)->test(Create::class))
            ->set('cpf', '11111111111')
            ->call('save')
            ->assertHasErrors(['cpf']);

        $this->assertNull(Person::where('email', 'maria@example.test')->first());
    }

    public function test_duplicate_cpf_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Person::factory()->create(['cpf' => '52998224725']);

        $this->fillPersonForm(Livewire::actingAs($admin)->test(Create::class))
            ->call('save')
            ->assertHasErrors(['cpf']);
    }

    public function test_admin_can_update_a_person(): void
    {
        $admin = User::factory()->admin()->create();
        $person = Person::factory()->create(['nome' => 'Old Name']);
        Address::factory()->for($person)->create();

        Livewire::actingAs($admin)
            ->test(Edit::class, ['personId' => $person->id])
            ->set('nome', 'New Name')
            ->call('save');

        $this->assertSame('New Name', $person->refresh()->nome);
    }

    public function test_cannot_delete_a_person_that_already_has_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $linked = User::factory()->developer()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $linked->person_id);

        $this->assertNotNull(Person::find($linked->person_id));
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/people');

        $response->assertForbidden();
    }

    public function test_livewire_create_rejects_non_admin(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Create::class)
            ->assertForbidden();
    }
}
