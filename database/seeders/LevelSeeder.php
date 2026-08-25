<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $xp = 0;

        for ($level = 1; $level <= 50; $level++) {
            $xp = match ($level) {
                1 => 0,
                2 => 100,
                3 => 250,
                default => $xp + (int) round(50 * ($level - 1) ** 1.5),
            };

            Level::firstOrCreate(['level' => $level], ['xp_required' => $xp]);
        }
    }
}
