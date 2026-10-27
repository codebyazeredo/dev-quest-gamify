<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->after('email')->constrained()->nullOnDelete();
            $table->foreignId('business_sector_id')->nullable()->after('address_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_sector_id');
            $table->dropConstrainedForeignId('address_id');
        });
    }
};
