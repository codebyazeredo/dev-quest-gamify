<?php

namespace Tests\Unit\Enums;

use App\Enums\XpSourceType;
use Tests\TestCase;

class XpSourceTypeTest extends TestCase
{
    public function test_values_match_the_spec(): void
    {
        $this->assertSame(1, XpSourceType::TASK->value);
        $this->assertSame(2, XpSourceType::TASK_EVENT->value);
        $this->assertSame(3, XpSourceType::ACHIEVEMENT->value);
        $this->assertSame(4, XpSourceType::CHECKIN->value);
        $this->assertSame(5, XpSourceType::CHALLENGE->value);
        $this->assertSame(6, XpSourceType::BONUS->value);
        $this->assertSame(7, XpSourceType::PENALTY->value);
        $this->assertSame(8, XpSourceType::ADMIN_ADJUSTMENT->value);
    }

    public function test_labels_are_defined_for_every_case(): void
    {
        foreach (XpSourceType::cases() as $type) {
            $this->assertNotEmpty($type->label());
        }
    }
}
