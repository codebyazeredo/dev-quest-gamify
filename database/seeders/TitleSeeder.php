<?php

namespace Database\Seeders;

use App\Enums\TitleUnlockType;
use App\Models\Achievement;
use App\Models\Challenge;
use App\Models\Title;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['name' => 'Bug Hunter', 'slug' => 'bug-hunter', 'icon' => 'bug', 'achievement_slug' => 'bug-hunter', 'active' => true],
            ['name' => 'Release Master', 'slug' => 'release-master', 'icon' => 'rocket', 'achievement_slug' => 'release-master', 'active' => true],
            ['name' => 'Speed Coder', 'slug' => 'speed-coder', 'icon' => 'bolt', 'achievement_slug' => 'speed-coder', 'active' => true],
            ['name' => 'Code Warrior', 'slug' => 'code-warrior', 'icon' => 'trophy', 'achievement_slug' => 'first-blood', 'active' => true],
            ['name' => 'Firefighter', 'slug' => 'firefighter', 'icon' => 'fire', 'achievement_slug' => null, 'active' => false],
            ['name' => 'Aprendiz de Caça', 'slug' => 'aprendiz-de-caca', 'icon' => 'target', 'achievement_slug' => 'primeira-caca', 'active' => true],
            ['name' => 'Entregador Nato', 'slug' => 'entregador-nato', 'icon' => 'gem', 'achievement_slug' => 'maquina-de-entregas', 'active' => true],
            ['name' => 'Sempre Presente', 'slug' => 'sempre-presente', 'icon' => 'fire', 'achievement_slug' => 'presenca-garantida', 'active' => true],
        ];

        foreach ($titles as $title) {
            $achievementId = $title['achievement_slug']
                ? Achievement::where('slug', $title['achievement_slug'])->value('id')
                : null;

            Title::firstOrCreate(
                ['slug' => $title['slug']],
                [
                    'name' => $title['name'],
                    'icon' => $title['icon'],
                    'unlock_type' => TitleUnlockType::ACHIEVEMENT,
                    'achievement_id' => $achievementId,
                    'active' => $title['active'],
                ]
            );
        }

        $this->seedChallengeTitles();
    }

    private function seedChallengeTitles(): void
    {
        $byChallenge = [
            ['name' => 'Campeão da Semana', 'slug' => 'campeao-da-semana', 'icon' => 'flag', 'challenge_slug' => 'desafio-da-semana'],
            ['name' => 'Guardião da Qualidade', 'slug' => 'guardiao-da-qualidade', 'icon' => 'shield', 'challenge_slug' => 'mes-da-qualidade'],
            ['name' => 'Maratonista de Deploys', 'slug' => 'maratonista-de-deploys', 'icon' => 'rocket', 'challenge_slug' => 'maratona-de-deploys'],
            ['name' => 'Veterano da Sprint', 'slug' => 'veterano-da-sprint', 'icon' => 'target', 'challenge_slug' => 'sprint-passada'],
        ];

        foreach ($byChallenge as $title) {
            Title::firstOrCreate(
                ['slug' => $title['slug']],
                [
                    'name' => $title['name'],
                    'icon' => $title['icon'],
                    'description' => 'Concedido ao concluir um desafio específico.',
                    'unlock_type' => TitleUnlockType::CHALLENGE,
                    'challenge_id' => Challenge::where('slug', $title['challenge_slug'])->value('id'),
                    'active' => true,
                ]
            );
        }

        $byCount = [
            ['name' => 'Desafiante', 'slug' => 'desafiante', 'icon' => 'compass', 'required' => 2],
            ['name' => 'Veterano de Desafios', 'slug' => 'veterano-de-desafios', 'icon' => 'medal', 'required' => 4],
            ['name' => 'Lenda do Código', 'slug' => 'lenda-do-codigo', 'icon' => 'code', 'required' => 6],
        ];

        foreach ($byCount as $title) {
            Title::firstOrCreate(
                ['slug' => $title['slug']],
                [
                    'name' => $title['name'],
                    'icon' => $title['icon'],
                    'description' => 'Concedido por concluir uma quantidade de desafios.',
                    'unlock_type' => TitleUnlockType::CHALLENGES_COUNT,
                    'challenges_required' => $title['required'],
                    'active' => true,
                ]
            );
        }
    }
}
