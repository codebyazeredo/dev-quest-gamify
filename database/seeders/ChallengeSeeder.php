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
                'icon' => 'flag',
                'type' => ChallengeType::TASKS_COMPLETED,
                'target' => 5,
                'xp_reward' => 100,
                'window' => 'week',
            ],
            [
                'name' => 'Bug Week',
                'slug' => 'bug-week',
                'description' => 'Resolver 10 bugs',
                'icon' => 'bug',
                'type' => ChallengeType::BUGS_RESOLVED,
                'target' => 10,
                'xp_reward' => 200,
                'window' => 'week',
            ],
            [
                'name' => 'Maratona de Deploys',
                'slug' => 'maratona-de-deploys',
                'description' => 'Coloque 6 entregas em produção neste mês',
                'icon' => 'rocket',
                'type' => ChallengeType::DEPLOYS_MADE,
                'target' => 6,
                'xp_reward' => 180,
                'window' => 'month',
            ],
            [
                'name' => 'Mês da Qualidade',
                'slug' => 'mes-da-qualidade',
                'description' => 'Resolva 8 bugs até o fim do mês',
                'icon' => 'shield',
                'type' => ChallengeType::BUGS_RESOLVED,
                'target' => 8,
                'xp_reward' => 250,
                'window' => 'month',
            ],
            [
                'name' => 'Presença Constante',
                'slug' => 'presenca-constante',
                'description' => 'Faça check-in em 5 dias desta semana',
                'icon' => 'fire',
                'type' => ChallengeType::CHECKINS_MADE,
                'target' => 5,
                'xp_reward' => 60,
                'window' => 'week',
            ],
            [
                'name' => 'Sprint Passada',
                'slug' => 'sprint-passada',
                'description' => 'Conclua 8 tarefas na sprint anterior',
                'icon' => 'target',
                'type' => ChallengeType::TASKS_COMPLETED,
                'target' => 8,
                'xp_reward' => 150,
                'window' => 'last_week',
            ],
            [
                'name' => 'Próxima Sprint',
                'slug' => 'proxima-sprint',
                'description' => 'Conclua 10 tarefas na próxima semana',
                'icon' => 'rocket',
                'type' => ChallengeType::TASKS_COMPLETED,
                'target' => 10,
                'xp_reward' => 200,
                'window' => 'next_week',
            ],
        ];

        foreach ($challenges as $challenge) {
            [$startsAt, $endsAt] = $this->windowFor($challenge['window']);
            unset($challenge['window']);

            Challenge::updateOrCreate(
                ['slug' => $challenge['slug']],
                array_merge($challenge, [
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'active' => true,
                ])
            );
        }
    }

    /** @return array{0: \Carbon\CarbonInterface, 1: \Carbon\CarbonInterface} */
    private function windowFor(string $window): array
    {
        return match ($window) {
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'next_week' => [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()],
            default => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }
}
