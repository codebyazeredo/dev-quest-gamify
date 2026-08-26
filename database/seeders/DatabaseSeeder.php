<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            LevelSeeder::class,
            BoardSeeder::class,
            BoardColumnSeeder::class,
            TaskCategorySeeder::class,
            TaskEventRuleSeeder::class,
            TaskPriorityRuleSeeder::class,
            AchievementSeeder::class,
            TitleSeeder::class,
            ChallengeSeeder::class,
        ]);
    }
}
