<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained();
            $table->foreignId('column_id')->constrained('board_columns');
            $table->foreignId('category_id')->constrained('task_categories');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('priority');
            $table->unsignedTinyInteger('status')->nullable();
            $table->unsignedInteger('position');
            $table->unsignedInteger('base_points');
            $table->decimal('priority_multiplier', 4, 2);
            $table->unsignedInteger('estimated_points')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['board_id', 'column_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
