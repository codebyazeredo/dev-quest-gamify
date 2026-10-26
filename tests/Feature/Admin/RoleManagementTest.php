<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.roles.store'), ['name' => 'financeiro'])->assertCreated();

        $this->assertNotNull(Role::where('name', 'financeiro')->first());
    }

    public function test_admin_can_toggle_a_permission_on_a_custom_role(): void
    {
        $admin = User::factory()->admin()->create();
        $role = Role::create(['name' => 'financeiro', 'guard_name' => 'web']);
        $permission = Permission::where('name', 'create-task')->firstOrFail();

        $this->actingAs($admin)->putJson(route('api.roles.permissions.toggle', [$role, $permission->id]))->assertOk();
        $this->assertTrue($role->fresh()->hasPermissionTo($permission));

        $this->actingAs($admin)->putJson(route('api.roles.permissions.toggle', [$role, $permission->id]))->assertOk();
        $this->assertFalse($role->fresh()->hasPermissionTo($permission));
    }

    public function test_admin_role_cannot_be_deleted_or_have_permissions_toggled(): void
    {
        $admin = User::factory()->admin()->create();
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $permission = Permission::where('name', 'manage-users')->firstOrFail();

        $this->actingAs($admin)->putJson(route('api.roles.permissions.toggle', [$adminRole, $permission->id]))->assertStatus(409);
        $this->assertTrue($adminRole->fresh()->hasPermissionTo($permission));

        $this->actingAs($admin)->deleteJson(route('api.roles.destroy', $adminRole))->assertStatus(409);
        $this->assertNotNull(Role::find($adminRole->id));
    }

    public function test_cannot_delete_a_role_that_is_assigned_to_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $devRole = Role::where('name', 'dev')->firstOrFail();

        $this->actingAs($admin)->deleteJson(route('api.roles.destroy', $devRole))->assertStatus(409);

        $this->assertNotNull(Role::find($devRole->id));
        $this->assertTrue($developer->fresh()->hasRole('dev'));
    }

    public function test_index_paginates_the_list_and_lists_permissions(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (range(1, 20) as $i) {
            Role::create(['name' => "custom-role-{$i}", 'guard_name' => 'web']);
        }

        $this->actingAs($admin)->getJson(route('api.roles.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonStructure(['permissions' => [['id', 'name', 'label']]]);
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/roles')->assertForbidden();
        $this->actingAs($developer)->getJson(route('api.roles.index'))->assertForbidden();
    }
}
