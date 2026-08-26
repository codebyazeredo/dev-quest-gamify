<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fake-but-checksum-valid CPF base digits (first 9) backfilled for the
     * pre-existing seeded users, keyed by their email, so `person_id` can
     * become NOT NULL without breaking them. Clearly not real people.
     */
    private const BACKFILL = [
        'admin@devquestgamify.test' => ['nome' => 'Admin', 'base' => '111444777'],
        'po@devquestgamify.test' => ['nome' => 'Product Owner', 'base' => '222555888'],
        'dev@devquestgamify.test' => ['nome' => 'Developer', 'base' => '333666999'],
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('person_id')->nullable()->after('id')->constrained();
        });

        $now = now();

        foreach (DB::table('users')->select('id', 'email')->get() as $user) {
            $info = self::BACKFILL[$user->email] ?? null;
            $base = $info['base'] ?? str_pad((string) $user->id, 9, '0', STR_PAD_LEFT);
            $cpf = $base.$this->checkDigits($base);

            $personId = DB::table('people')->insertGetId([
                'nome' => $info['nome'] ?? 'Usuário #'.$user->id,
                'cpf' => $cpf,
                'rg' => null,
                'nascimento' => '1990-01-01',
                'sexo' => 3, // Gender::OTHER — placeholder for backfilled system accounts
                'email' => $user->email,
                'telefone1' => '00000000000',
                'telefone2' => null,
                'foto_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('users')->where('id', $user->id)->update(['person_id' => $personId]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('person_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('person_id');
        });
    }

    private function checkDigits(string $base9): string
    {
        $digits = $base9;

        for ($round = 0; $round < 2; $round++) {
            $length = strlen($digits);
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $digits[$i] * (($length + 1) - $i);
            }

            $check = ($sum * 10) % 11;
            $digits .= (string) ($check === 10 ? 0 : $check);
        }

        return substr($digits, -2);
    }
};
