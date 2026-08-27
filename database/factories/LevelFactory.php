<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'level' => fake()->unique()->numberBetween(1, 50),
            'xp_required' => fake()->numberBetween(0, 300000),
        ];
    }
}
