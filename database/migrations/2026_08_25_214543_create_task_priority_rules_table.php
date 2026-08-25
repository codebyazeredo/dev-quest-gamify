<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_priority_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('priority')->unique();
            $table->decimal('multiplier', 4, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_priority_rules');
    }
};
