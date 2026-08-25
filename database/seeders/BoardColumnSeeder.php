<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardColumn;
use Illuminate\Database\Seeder;

class BoardColumnSeeder extends Seeder
{
    public function run(): void
    {
        Board::all()->each(function (Board $board) {
            if ($board->columns()->exists()) {
                return;
            }

            BoardColumn::seedDefaultsFor($board);
        });
    }
}
