<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'ticket_message_id' => null,
            'user_id' => null,
            'original_name' => fake()->word().'.png',
            'path' => 'ticket-attachments/'.fake()->uuid().'.png',
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(1000, 500000),
        ];
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'original_name' => fake()->word().'.txt',
            'path' => 'ticket-attachments/'.fake()->uuid().'.txt',
            'mime_type' => 'text/plain',
        ]);
    }
}
