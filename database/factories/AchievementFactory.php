<?php

namespace Database\Factories;

use App\Enums\AchievementConditionType;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word().' '.fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => '🏆',
            'condition_type' => fake()->randomElement(AchievementConditionType::cases()),
            'condition_value' => fake()->numberBetween(1, 10),
            'xp_reward' => fake()->numberBetween(10, 100),
            'active' => true,
        ];
    }
}
