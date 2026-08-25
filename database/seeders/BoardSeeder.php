<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        Board::firstOrCreate(
            ['name' => 'Sistema Principal'],
            [
                'description' => 'Board padrão para desenvolvimento do sistema.',
                'is_active' => true,
            ]
        );
    }
}
