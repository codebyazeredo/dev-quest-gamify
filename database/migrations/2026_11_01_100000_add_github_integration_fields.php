<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->string('github_repository', 140)->nullable()->unique()->after('description');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('pull_request_state', 20)->nullable()->after('pull_request_url');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('pull_request_state');
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->dropUnique(['github_repository']);
            $table->dropColumn('github_repository');
        });
    }
};
