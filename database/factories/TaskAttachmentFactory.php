<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'original_name' => fake()->word().'.pdf',
            'path' => 'task-attachments/'.fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 500000),
        ];
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'original_name' => fake()->word().'.png',
            'path' => 'task-attachments/'.fake()->uuid().'.png',
            'mime_type' => 'image/png',
        ]);
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'original_name' => fake()->word().'.txt',
            'path' => 'task-attachments/'.fake()->uuid().'.txt',
            'mime_type' => 'text/plain',
        ]);
    }
}
