<?php

namespace Database\Factories;

use App\Enums\TaskEventType;
use App\Models\Task;
use App\Models\TaskEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskEvent>
 */
class TaskEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'type' => fake()->randomElement(TaskEventType::cases()),
            'user_id' => User::factory(),
            'occurred_at' => now(),
        ];
    }
}
