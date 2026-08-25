<?php

namespace Tests\Unit\Models;

use App\Enums\TaskPriority;
use App\Models\TaskPriorityRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPriorityRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiplier_for_falls_back_to_the_enum_default_when_no_override_exists(): void
    {
        $this->assertSame(5.0, TaskPriorityRule::multiplierFor(TaskPriority::CRITICAL));
        $this->assertSame(1.5, TaskPriorityRule::multiplierFor(TaskPriority::NORMAL));
    }

    public function test_multiplier_for_prefers_the_admin_edited_override(): void
    {
        TaskPriorityRule::factory()->create(['priority' => TaskPriority::CRITICAL, 'multiplier' => 7.25]);

        $this->assertSame(7.25, TaskPriorityRule::multiplierFor(TaskPriority::CRITICAL));
        $this->assertSame(1.5, TaskPriorityRule::multiplierFor(TaskPriority::NORMAL));
    }
}
