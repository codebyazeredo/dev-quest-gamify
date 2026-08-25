<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);
            $table->string('slug', 60);
            $table->unsignedInteger('position');
            $table->boolean('is_final')->default(false);
            $table->unsignedTinyInteger('status');
            $table->timestamps();

            $table->index(['board_id', 'position']);
            $table->unique(['board_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_columns');
    }
};
