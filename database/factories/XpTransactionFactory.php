<?php

namespace Database\Factories;

use App\Enums\XpSourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class XpTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(1, 50),
            'source_type' => XpSourceType::BONUS,
            'source_id' => null,
            'description' => fake()->sentence(3),
        ];
    }
}
