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
                'icon' => 'bug',
                'condition_type' => AchievementConditionType::BUGS_RESOLVED,
                'condition_value' => 10,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Release Master',
                'slug' => 'release-master',
                'description' => 'Faça 10 deploys',
                'icon' => 'rocket',
                'condition_type' => AchievementConditionType::DEPLOYS_MADE,
                'condition_value' => 10,
                'xp_reward' => 150,
            ],
            [
                'name' => 'Speed Coder',
                'slug' => 'speed-coder',
                'description' => 'Conclua 5 tarefas em um dia',
                'icon' => 'bolt',
                'condition_type' => AchievementConditionType::TASKS_COMPLETED_IN_A_DAY,
                'condition_value' => 5,
                'xp_reward' => 120,
            ],
            [
                'name' => 'First Blood',
                'slug' => 'first-blood',
                'description' => 'Conclua sua primeira tarefa',
                'icon' => 'trophy',
                'condition_type' => AchievementConditionType::TASKS_COMPLETED_TOTAL,
                'condition_value' => 1,
                'xp_reward' => 25,
            ],
            [
                'name' => 'Primeira Caça',
                'slug' => 'primeira-caca',
                'description' => 'Resolva 3 bugs',
                'icon' => 'target',
                'condition_type' => AchievementConditionType::BUGS_RESOLVED,
                'condition_value' => 3,
                'xp_reward' => 40,
            ],
            [
                'name' => 'Máquina de Entregas',
                'slug' => 'maquina-de-entregas',
                'description' => 'Conclua 15 tarefas',
                'icon' => 'gem',
                'condition_type' => AchievementConditionType::TASKS_COMPLETED_TOTAL,
                'condition_value' => 15,
                'xp_reward' => 90,
            ],
            [
                'name' => 'Presença Garantida',
                'slug' => 'presenca-garantida',
                'description' => 'Faça 10 check-ins',
                'icon' => 'fire',
                'condition_type' => AchievementConditionType::CHECKINS_TOTAL,
                'condition_value' => 10,
                'xp_reward' => 50,
            ],
            [
                'name' => 'Caçador de Desafios',
                'slug' => 'cacador-de-desafios',
                'description' => 'Conclua 2 desafios',
                'icon' => 'flag',
                'condition_type' => AchievementConditionType::CHALLENGES_COMPLETED,
                'condition_value' => 2,
                'xp_reward' => 80,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(['slug' => $achievement['slug']], $achievement);
        }
    }
}
