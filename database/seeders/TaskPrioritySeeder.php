<?php

namespace Database\Seeders;

use App\Models\TaskPriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            ['name' => 'Baixa', 'multiplier' => '1.00'],
            ['name' => 'Normal', 'multiplier' => '1.50'],
            ['name' => 'Alta', 'multiplier' => '2.00'],
            ['name' => 'Crítica', 'multiplier' => '5.00'],
        ];

        foreach ($priorities as $priority) {
            TaskPriority::firstOrCreate(
                ['slug' => Str::slug($priority['name'])],
                [
                    'name' => $priority['name'],
                    'multiplier' => $priority['multiplier'],
                ]
            );
        }
    }
}
