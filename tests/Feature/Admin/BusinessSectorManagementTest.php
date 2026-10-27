<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessSector;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessSectorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_sector(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.business-sectors.store'), ['name' => 'Fintech'])
            ->assertCreated();

        $sector = BusinessSector::where('name', 'Fintech')->firstOrFail();

        $this->actingAs($admin)
            ->putJson(route('api.business-sectors.update', $sector), ['name' => 'Serviços Financeiros'])
            ->assertOk();

        $this->assertSame('Serviços Financeiros', $sector->refresh()->name);

        $this->actingAs($admin)->deleteJson(route('api.business-sectors.destroy', $sector))->assertOk();

        $this->assertNull(BusinessSector::find($sector->id));
    }

    public function test_sector_names_are_unique_and_required(): void
    {
        $admin = User::factory()->admin()->create();
        BusinessSector::factory()->create(['name' => 'Educação']);

        $this->actingAs($admin)->postJson(route('api.business-sectors.store'), ['name' => 'Educação'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->actingAs($admin)->postJson(route('api.business-sectors.store'), ['name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_deleting_a_sector_with_clients_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $sector = BusinessSector::factory()->create();
        Client::factory()->create(['business_sector_id' => $sector->id]);

        $this->actingAs($admin)->deleteJson(route('api.business-sectors.destroy', $sector))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Este ramo de atuação ainda possui clientes.');

        $this->assertNotNull(BusinessSector::find($sector->id));
    }

    public function test_index_lists_the_client_count(): void
    {
        $admin = User::factory()->admin()->create();
        $sector = BusinessSector::factory()->create();
        Client::factory()->count(2)->create(['business_sector_id' => $sector->id]);

        $response = $this->actingAs($admin)->getJson(route('api.business-sectors.index'))->assertOk();

        $this->assertSame(2, collect($response->json('data'))->firstWhere('id', $sector->id)['clients_count']);
    }

    public function test_developer_is_forbidden(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/business-sectors')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.business-sectors.index'))->assertForbidden();
    }
}
