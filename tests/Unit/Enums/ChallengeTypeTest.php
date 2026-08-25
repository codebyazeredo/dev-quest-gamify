<?php

namespace Tests\Unit\Enums;

use App\Enums\ChallengeType;
use Tests\TestCase;

class ChallengeTypeTest extends TestCase
{
    public function test_labels_are_defined_for_every_case(): void
    {
        foreach (ChallengeType::cases() as $type) {
            $this->assertNotEmpty($type->label());
        }
    }
}
