<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_role(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Roles::class)
            ->set('name', 'financeiro')
            ->call('create');

        $this->assertNotNull(Role::where('name', 'financeiro')->first());
    }

    public function test_admin_can_toggle_a_permission_on_a_custom_role(): void
    {
        $admin = User::factory()->admin()->create();
        $role = Role::create(['name' => 'financeiro', 'guard_name' => 'web']);
        $permission = Permission::where('name', 'create-task')->first();

        $component = Livewire::actingAs($admin)->test(Roles::class);

        $component->call('togglePermission', $role->id, $permission->id);
        $this->assertTrue($role->fresh()->hasPermissionTo($permission));

        $component->call('togglePermission', $role->id, $permission->id);
        $this->assertFalse($role->fresh()->hasPermissionTo($permission));
    }

    public function test_admin_role_cannot_be_deleted_or_have_permissions_toggled(): void
    {
        $admin = User::factory()->admin()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $permission = Permission::where('name', 'manage-users')->first();

        $component = Livewire::actingAs($admin)->test(Roles::class);

        $component->call('togglePermission', $adminRole->id, $permission->id);
        $this->assertTrue($adminRole->fresh()->hasPermissionTo($permission));

        $component->call('delete', $adminRole->id);
        $this->assertNotNull(Role::find($adminRole->id));
    }

    public function test_cannot_delete_a_role_that_is_assigned_to_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $developer = User::factory()->developer()->create();
        $devRole = Role::where('name', 'dev')->first();

        Livewire::actingAs($admin)
            ->test(Roles::class)
            ->call('delete', $devRole->id);

        $this->assertNotNull(Role::find($devRole->id));
        $this->assertTrue($developer->fresh()->hasRole('dev'));
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/roles');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Roles::class)
            ->assertForbidden();
    }
}
