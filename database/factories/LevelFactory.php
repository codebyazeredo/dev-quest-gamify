<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Level>
 */
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
