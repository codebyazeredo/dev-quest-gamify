<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BoardColumnFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());

        return [
            'board_id' => Board::factory(),
            'name' => $status->label(),
            'slug' => Str::slug($status->label()).'-'.fake()->unique()->numberBetween(1, 100000),
            'position' => fake()->numberBetween(0, 10),
            'is_final' => $status === TaskStatus::DONE,
            'status' => $status,
        ];
    }

    public function status(TaskStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $status->label(),
            'slug' => Str::slug($status->label()).'-'.fake()->unique()->numberBetween(1, 100000),
            'is_final' => $status === TaskStatus::DONE,
            'status' => $status,
        ]);
    }

    public function untagged(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->words(2, true),
            'slug' => Str::slug(fake()->words(2, true)).'-'.fake()->unique()->numberBetween(1, 100000),
            'is_final' => false,
            'status' => null,
        ]);
    }
}
