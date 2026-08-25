<?php

namespace Tests\Unit\Enums;

use App\Enums\TaskPriority;
use Tests\TestCase;

class TaskPriorityTest extends TestCase
{
    public function test_multipliers_match_the_spec(): void
    {
        $this->assertSame(1.00, TaskPriority::LOW->multiplier());
        $this->assertSame(1.50, TaskPriority::NORMAL->multiplier());
        $this->assertSame(2.00, TaskPriority::HIGH->multiplier());
        $this->assertSame(5.00, TaskPriority::CRITICAL->multiplier());
    }

    public function test_labels_are_defined_for_every_case(): void
    {
        foreach (TaskPriority::cases() as $priority) {
            $this->assertNotEmpty($priority->label());
        }
    }
}
