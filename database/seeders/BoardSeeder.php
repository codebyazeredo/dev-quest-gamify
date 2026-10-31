<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $boards = [
            [
                'name' => 'TMS Core',
                'description' => 'Cotação e tabelas de frete, roteirização, cargas, faturamento e relatórios do TMS.',
                'github_repository' => 'rotamax/tms-core',
                'is_active' => true,
            ],
            [
                'name' => 'Integrações e Plataforma',
                'description' => 'SEFAZ (CT-e e MDF-e), ERPs, rastreamento, app do motorista, API e infraestrutura.',
                'github_repository' => 'rotamax/tms-integracoes',
                'is_active' => true,
            ],
        ];

        foreach ($boards as $board) {
            Board::firstOrCreate(['name' => $board['name']], $board);
        }
    }
}
