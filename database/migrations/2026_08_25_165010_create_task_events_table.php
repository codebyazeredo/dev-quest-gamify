<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('type');
            $table->foreignId('user_id')->constrained();
            $table->timestamp('occurred_at');
            $table->timestamp('created_at');

            $table->unique(['task_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_events');
    }
};
