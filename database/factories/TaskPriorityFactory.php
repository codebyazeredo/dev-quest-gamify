<?php

namespace Database\Factories;

use App\Models\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TaskPriority>
 */
class TaskPriorityFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'multiplier' => fake()->randomFloat(2, 1, 5),
        ];
    }
}
