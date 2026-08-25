<?php

namespace Database\Factories;

use App\Models\DailyCheckin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyCheckin>
 */
class DailyCheckinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => now()->toDateString(),
            'streak_count' => 1,
        ];
    }
}
