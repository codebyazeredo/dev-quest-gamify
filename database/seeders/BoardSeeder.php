<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $boards = [
            ['name' => 'Sistema Principal', 'description' => 'Board padrão para desenvolvimento do sistema.', 'is_active' => true],
            ['name' => 'Aplicativo Mobile', 'description' => 'Aplicativo para iOS e Android usado pelos clientes.', 'is_active' => true],
            ['name' => 'Portal do Cliente', 'description' => 'Área logada onde os clientes acompanham seus pedidos.', 'is_active' => true],
            ['name' => 'Infraestrutura e DevOps', 'description' => 'Pipelines, monitoramento, backups e melhorias de ambiente.', 'is_active' => true],
            ['name' => 'Projeto Legado', 'description' => 'Sistema antigo em manutenção mínima.', 'is_active' => false],
        ];

        foreach ($boards as $board) {
            Board::firstOrCreate(['name' => $board['name']], $board);
        }
    }
}
