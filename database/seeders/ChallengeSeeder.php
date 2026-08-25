<?php

namespace Database\Seeders;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $challenges = [
            [
                'name' => 'Desafio da Semana',
                'slug' => 'desafio-da-semana',
                'description' => 'Complete 5 tarefas',
                'type' => ChallengeType::TASKS_COMPLETED,
                'target' => 5,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Bug Week',
                'slug' => 'bug-week',
                'description' => 'Resolver 10 bugs',
                'type' => ChallengeType::BUGS_RESOLVED,
                'target' => 10,
                'xp_reward' => 200,
            ],
        ];

        foreach ($challenges as $challenge) {
            Challenge::updateOrCreate(
                ['slug' => $challenge['slug']],
                array_merge($challenge, [
                    'starts_at' => now()->startOfWeek(),
                    'ends_at' => now()->endOfWeek(),
                    'active' => true,
                ])
            );
        }
    }
}
