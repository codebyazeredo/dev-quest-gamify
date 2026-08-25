<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserChallenge>
 */
class UserChallengeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'challenge_id' => Challenge::factory(),
            'progress' => 0,
            'completed_at' => null,
        ];
    }
}
