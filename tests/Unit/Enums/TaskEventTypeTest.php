<?php

namespace Tests\Unit\Enums;

use App\Enums\TaskEventType;
use Tests\TestCase;

class TaskEventTypeTest extends TestCase
{
    public function test_values_match_the_spec(): void
    {
        $this->assertSame(1, TaskEventType::STARTED->value);
        $this->assertSame(2, TaskEventType::DEVELOPMENT_COMPLETED->value);
        $this->assertSame(3, TaskEventType::REVIEW_COMPLETED->value);
        $this->assertSame(4, TaskEventType::TEST_COMPLETED->value);
        $this->assertSame(5, TaskEventType::HOMOLOGATION_COMPLETED->value);
        $this->assertSame(6, TaskEventType::DEPLOYED->value);
        $this->assertSame(7, TaskEventType::COMPLETED->value);
    }

    public function test_labels_are_defined_for_every_case(): void
    {
        foreach (TaskEventType::cases() as $type) {
            $this->assertNotEmpty($type->label());
        }
    }
}
