<?php

namespace Tests\Unit\Enums;

use App\Enums\AchievementConditionType;
use Tests\TestCase;

class AchievementConditionTypeTest extends TestCase
{
    public function test_labels_are_defined_for_every_case(): void
    {
        foreach (AchievementConditionType::cases() as $type) {
            $this->assertNotEmpty($type->label());
        }
    }
}
