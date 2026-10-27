<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->char('iso2', 2)->unique();
            $table->timestamps();
        });

        $brazilId = DB::table('countries')->insertGetId(['name' => 'Brasil', 'iso2' => 'BR', 'created_at' => now(), 'updated_at' => now()]);

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('id')->constrained();
            $table->string('complemento', 100)->nullable()->after('numero');
            $table->string('bairro', 120)->nullable()->after('complemento');
            $table->string('referencia')->nullable()->after('estado');
            $table->decimal('latitude', 10, 7)->nullable()->after('referencia');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->index('cep');
        });

        DB::table('addresses')->update(['country_id' => $brazilId]);

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable(false)->change();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->after('foto_path')->constrained()->nullOnDelete();
        });

        DB::statement('UPDATE people SET address_id = (SELECT MIN(addresses.id) FROM addresses WHERE addresses.person_id = people.id)');

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('person_id');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('person_id')->nullable()->constrained()->cascadeOnDelete();
        });

        DB::statement('UPDATE addresses SET person_id = (SELECT MIN(people.id) FROM people WHERE people.address_id = addresses.id)');
        DB::table('addresses')->whereNull('person_id')->delete();

        Schema::table('people', function (Blueprint $table) {
            $table->dropConstrainedForeignId('address_id');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex(['cep']);
            $table->dropConstrainedForeignId('country_id');
            $table->dropColumn(['complemento', 'bairro', 'referencia', 'latitude', 'longitude']);
        });

        Schema::dropIfExists('countries');
    }
};
