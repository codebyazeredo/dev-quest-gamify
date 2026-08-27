<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    private const PERMISSIONS = [
        'create-task',
        'move-task',
        'test-task',
        'manage-board',
        'assign-task',
        'manage-users',
        'manage-people',
        'manage-admin-settings',
    ];

    private const ROLE_PERMISSIONS = [
        'admin' => self::PERMISSIONS,
        'product_owner' => ['create-task', 'move-task', 'manage-board', 'assign-task'],
        'dev' => ['move-task'],
        'tester' => ['test-task'],
        'suporte' => ['create-task'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
