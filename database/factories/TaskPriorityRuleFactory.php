<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\TaskPriorityRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskPriorityRule>
 */
class TaskPriorityRuleFactory extends Factory
{
    public function definition(): array
    {
        $priority = fake()->randomElement(TaskPriority::cases());

        return [
            'priority' => $priority,
            'multiplier' => $priority->multiplier(),
        ];
    }
}
