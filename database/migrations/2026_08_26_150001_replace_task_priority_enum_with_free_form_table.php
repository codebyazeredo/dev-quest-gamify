<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug', 60)->unique();
            $table->decimal('multiplier', 4, 2);
            $table->timestamps();
        });

        $defaults = [
            1 => ['name' => 'Baixa', 'multiplier' => '1.00'],
            2 => ['name' => 'Normal', 'multiplier' => '1.50'],
            3 => ['name' => 'Alta', 'multiplier' => '2.00'],
            4 => ['name' => 'Crítica', 'multiplier' => '5.00'],
        ];

        $overrides = Schema::hasTable('task_priority_rules')
            ? DB::table('task_priority_rules')->pluck('multiplier', 'priority')
            : collect();

        $idMap = [];
        $now = now();

        foreach ($defaults as $oldValue => $default) {
            $idMap[$oldValue] = DB::table('task_priorities')->insertGetId([
                'name' => $default['name'],
                'slug' => Str::slug($default['name']),
                'multiplier' => $overrides[$oldValue] ?? $default['multiplier'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('priority_id')->nullable()->after('priority')->constrained('task_priorities');
        });

        foreach ($idMap as $oldValue => $newId) {
            DB::table('tasks')->where('priority', $oldValue)->update(['priority_id' => $newId]);
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('priority_id')->nullable(false)->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('priority');
        });

        Schema::dropIfExists('task_priority_rules');
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('priority')->nullable()->after('priority_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('priority_id');
        });

        Schema::dropIfExists('task_priorities');
    }
};
