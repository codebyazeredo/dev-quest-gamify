<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('due_at');
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->after('rejected_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['rejection_reason', 'rejected_at']);
        });
    }
};
