<?php

namespace Database\Factories;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word().' '.fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(ChallengeType::cases()),
            'target' => fake()->numberBetween(1, 10),
            'xp_reward' => fake()->numberBetween(10, 200),
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->endOfWeek(),
            'active' => true,
        ];
    }
}
