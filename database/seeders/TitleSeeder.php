<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Title;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['name' => 'Bug Hunter', 'slug' => 'bug-hunter', 'icon' => '🐛', 'achievement_slug' => 'bug-hunter', 'active' => true],
            ['name' => 'Release Master', 'slug' => 'release-master', 'icon' => '🚀', 'achievement_slug' => 'release-master', 'active' => true],
            ['name' => 'Speed Coder', 'slug' => 'speed-coder', 'icon' => '⚡', 'achievement_slug' => 'speed-coder', 'active' => true],
            ['name' => 'Code Warrior', 'slug' => 'code-warrior', 'icon' => '🏆', 'achievement_slug' => 'first-blood', 'active' => true],
            ['name' => 'Firefighter', 'slug' => 'firefighter', 'icon' => '🔥', 'achievement_slug' => null, 'active' => false],
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
                    'achievement_id' => $achievementId,
                    'active' => $title['active'],
                ]
            );
        }
    }
}
