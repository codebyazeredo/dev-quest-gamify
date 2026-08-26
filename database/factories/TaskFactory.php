<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $priority = TaskPriority::factory()->create();

        return [
            'board_id' => Board::factory(),
            'column_id' => BoardColumn::factory(),
            'category_id' => TaskCategory::factory(),
            'priority_id' => $priority->id,
            'assigned_to' => null,
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'position' => 0,
            'base_points' => fake()->numberBetween(5, 20),
            'priority_multiplier' => $priority->multiplier,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
        ]);
    }
}
