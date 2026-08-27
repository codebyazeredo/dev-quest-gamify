<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ROLE_MAP = [
        1 => 'admin',
        2 => 'product_owner',
        3 => 'dev',
    ];

    public function up(): void
    {
        $now = now();

        DB::table('roles')->insertOrIgnore([
            ['name' => 'admin', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'product_owner', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'dev', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tester', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'suporte', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $roleIds = DB::table('roles')->whereIn('name', self::ROLE_MAP)->pluck('id', 'name');

        foreach (DB::table('users')->select('id', 'role')->get() as $user) {
            $roleName = self::ROLE_MAP[$user->role] ?? null;

            if ($roleName === null) {
                continue;
            }

            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $roleIds[$roleName],
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('role')->default(3)->after('password');
        });

        $roleIds = DB::table('roles')->whereIn('name', self::ROLE_MAP)->pluck('id', 'name')->flip();

        foreach (DB::table('model_has_roles')->where('model_type', 'App\\Models\\User')->get() as $pivot) {
            $roleName = $roleIds[$pivot->role_id] ?? null;
            $oldValue = $roleName !== null ? array_search($roleName, self::ROLE_MAP, true) : null;

            if ($oldValue !== null) {
                DB::table('users')->where('id', $pivot->model_id)->update(['role' => $oldValue]);
            }
        }
    }
};
