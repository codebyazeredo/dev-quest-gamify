<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Models\TaskPriorityRule;
use Illuminate\Database\Seeder;

class TaskPriorityRuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (TaskPriority::cases() as $priority) {
            TaskPriorityRule::firstOrCreate(
                ['priority' => $priority],
                ['multiplier' => $priority->multiplier()]
            );
        }
    }
}
