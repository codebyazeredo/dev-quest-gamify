<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => TicketStatus::OPEN,
        ];
    }
}
