<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The default (3) is a snapshot of what UserRole::DEVELOPER->value was at
     * the time this migration was written — hardcoded rather than referencing
     * the enum, since the enum itself later changed (int-backed -> string-backed
     * role names) and a migration must stay replayable against its own history.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('role')->default(3)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
