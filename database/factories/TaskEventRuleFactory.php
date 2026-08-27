<?php

namespace Database\Factories;

use App\Enums\TaskEventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskEventRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->unique()->randomElement(TaskEventType::cases()),
            'xp_reward' => fake()->numberBetween(0, 20),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
