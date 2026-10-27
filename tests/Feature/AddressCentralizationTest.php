<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Country;
use App\Models\Person;
use App\Models\User;
use App\Services\AddressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressCentralizationTest extends TestCase
{
    use RefreshDatabase;

    private const ADDRESS = [
        'cep' => '01310-100',
        'logradouro' => 'Av. Paulista',
        'numero' => '1000',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ];

    private function personPayload(string $cpf, string $email, array $overrides = []): array
    {
        return [
            'nome' => 'Pessoa Teste',
            'cpf' => $cpf,
            'nascimento' => '1990-01-01',
            'sexo' => 2,
            'email' => $email,
            'telefone1' => '11999998888',
            ...self::ADDRESS,
            ...$overrides,
        ];
    }

    public function test_the_same_address_is_stored_once_and_defaults_to_brazil(): void
    {
        $service = app(AddressService::class);

        $first = $service->resolve(self::ADDRESS);
        $second = $service->resolve([...self::ADDRESS, 'cep' => '01310100', 'estado' => 'sp']);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Address::count());
        $this->assertSame('BR', $first->country->iso2);
        $this->assertSame('01310-100', $first->cep);
    }

    public function test_a_different_country_or_complement_creates_another_address(): void
    {
        $service = app(AddressService::class);
        $portugal = Country::factory()->create();

        $base = $service->resolve(self::ADDRESS);

        $this->assertNotSame($base->id, $service->resolve([...self::ADDRESS, 'complemento' => 'Sala 2'])->id);
        $this->assertNotSame($base->id, $service->resolve([...self::ADDRESS, 'country_id' => $portugal->id])->id);
    }

    public function test_formatted_address_joins_every_filled_part(): void
    {
        $address = app(AddressService::class)->resolve([...self::ADDRESS, 'complemento' => 'Sala 2', 'bairro' => 'Bela Vista']);

        $this->assertSame('Av. Paulista, 1000 - Sala 2 - Bela Vista, São Paulo/SP, 01310-100, Brasil', $address->formatted);
    }

    public function test_two_people_at_the_same_address_share_one_row(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.people.store'), $this->personPayload('52998224725', 'a@example.test'))->assertCreated();
        $this->actingAs($admin)->postJson(route('api.people.store'), $this->personPayload('11144477735', 'b@example.test'))->assertCreated();

        $this->assertSame(1, Address::count());
        $this->assertSame(2, Address::firstOrFail()->people()->count());
    }

    public function test_moving_one_person_keeps_the_other_and_removes_unused_addresses(): void
    {
        $admin = User::factory()->admin()->create();
        $shared = Address::factory()->create();
        $stays = Person::factory()->withAddress($shared)->create();
        $moves = Person::factory()->withAddress($shared)->create();

        $this->actingAs($admin)
            ->putJson(route('api.people.update', $moves), $this->personPayload($moves->cpf, $moves->email, ['logradouro' => 'Rua Nova']))
            ->assertOk();

        $this->assertSame($shared->id, $stays->refresh()->address_id);
        $this->assertNotSame($shared->id, $moves->refresh()->address_id);
        $this->assertSame('Rua Nova', $moves->address->logradouro);

        $this->actingAs($admin)->deleteJson(route('api.people.destroy', $moves))->assertOk();

        $this->assertNull(Address::where('logradouro', 'Rua Nova')->first());
        $this->assertNotNull(Address::find($shared->id));
    }

    public function test_person_resource_exposes_the_new_address_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $address = app(AddressService::class)->resolve([...self::ADDRESS, 'bairro' => 'Bela Vista', 'latitude' => -23.5614, 'longitude' => -46.6558]);
        $person = Person::factory()->withAddress($address)->create();

        $response = $this->actingAs($admin)->getJson(route('api.people.index'))->assertOk();

        $row = collect($response->json('data'))->firstWhere('id', $person->id);

        $this->assertSame('Bela Vista', $row['bairro']);
        $this->assertSame($address->country_id, $row['country_id']);
        $this->assertSame('-23.5614000', $row['latitude']);
        $response->assertJsonPath('default_country_id', $address->country_id);
    }
}
