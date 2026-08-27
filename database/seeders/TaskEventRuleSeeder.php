<?php

namespace Database\Seeders;

use App\Enums\TaskEventType;
use App\Models\TaskEventRule;
use Illuminate\Database\Seeder;

class TaskEventRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['type' => TaskEventType::STARTED, 'xp_reward' => 0, 'active' => false],
            ['type' => TaskEventType::DEVELOPMENT_COMPLETED, 'xp_reward' => 10, 'active' => true],
            ['type' => TaskEventType::REVIEW_COMPLETED, 'xp_reward' => 10, 'active' => true],
            ['type' => TaskEventType::TEST_COMPLETED, 'xp_reward' => 5, 'active' => true],
            ['type' => TaskEventType::HOMOLOGATION_COMPLETED, 'xp_reward' => 5, 'active' => true],
            ['type' => TaskEventType::DEPLOYED, 'xp_reward' => 20, 'active' => true],
            ['type' => TaskEventType::COMPLETED, 'xp_reward' => 0, 'active' => false],

            ['type' => TaskEventType::APPROVED, 'xp_reward' => 50, 'active' => true],
            ['type' => TaskEventType::CREATION_COMPLETED, 'xp_reward' => 25, 'active' => true],
        ];

        foreach ($rules as $rule) {
            TaskEventRule::firstOrCreate(
                ['type' => $rule['type']],
                ['xp_reward' => $rule['xp_reward'], 'active' => $rule['active']]
            );
        }
    }
}
