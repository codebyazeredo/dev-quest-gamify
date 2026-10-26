<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->string('unlock_type', 30)->default('achievement')->after('icon');
            $table->foreignId('challenge_id')->nullable()->after('achievement_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('challenges_required')->nullable()->after('challenge_id');
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->string('icon', 20)->default('flag')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('challenge_id');
            $table->dropColumn(['unlock_type', 'challenges_required']);
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
