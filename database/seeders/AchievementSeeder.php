<?php

namespace Database\Seeders;

use App\Enums\AchievementConditionType;
use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Bug Hunter',
                'slug' => 'bug-hunter',
                'description' => 'Resolva 10 bugs',
                'icon' => '🐛',
                'condition_type' => AchievementConditionType::BUGS_RESOLVED,
                'condition_value' => 10,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Release Master',
                'slug' => 'release-master',
                'description' => 'Faça 10 deploys',
                'icon' => '🚀',
                'condition_type' => AchievementConditionType::DEPLOYS_MADE,
                'condition_value' => 10,
                'xp_reward' => 150,
            ],
            [
                'name' => 'Speed Coder',
                'slug' => 'speed-coder',
                'description' => 'Conclua 5 tarefas em um dia',
                'icon' => '⚡',
                'condition_type' => AchievementConditionType::TASKS_COMPLETED_IN_A_DAY,
                'condition_value' => 5,
                'xp_reward' => 120,
            ],
            [
                'name' => 'First Blood',
                'slug' => 'first-blood',
                'description' => 'Conclua sua primeira tarefa',
                'icon' => '🏆',
                'condition_type' => AchievementConditionType::TASKS_COMPLETED_TOTAL,
                'condition_value' => 1,
                'xp_reward' => 25,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(['slug' => $achievement['slug']], $achievement);
        }
    }
}
