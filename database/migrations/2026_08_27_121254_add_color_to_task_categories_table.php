<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_categories', function (Blueprint $table) {
            $table->string('color', 7)->default('#e2e5ea')->after('base_points');
            $table->string('text_color', 7)->default('#1b2733')->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('task_categories', function (Blueprint $table) {
            $table->dropColumn(['color', 'text_color']);
        });
    }
};
