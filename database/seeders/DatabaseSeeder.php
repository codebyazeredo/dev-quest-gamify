<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CountrySeeder::class,
            UserSeeder::class,
            LevelSeeder::class,
            BoardSeeder::class,
            BoardColumnSeeder::class,
            TaskCategorySeeder::class,
            TaskEventRuleSeeder::class,
            TaskPrioritySeeder::class,
            AchievementSeeder::class,
            ChallengeSeeder::class,
            TitleSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
