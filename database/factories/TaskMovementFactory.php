<?php

namespace Database\Factories;

use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\TaskMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskMovement>
 */
class TaskMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'from_column_id' => BoardColumn::factory(),
            'to_column_id' => BoardColumn::factory(),
            'user_id' => User::factory(),
            'note' => null,
            'created_at' => now(),
        ];
    }
}
