<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bug', 'base_points' => 10],
            ['name' => 'Feature', 'base_points' => 20],
            ['name' => 'Refactoring', 'base_points' => 15],
            ['name' => 'Improvement', 'base_points' => 15],
            ['name' => 'Infrastructure', 'base_points' => 20],
            ['name' => 'Documentation', 'base_points' => 5],
        ];

        foreach ($categories as $category) {
            TaskCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'base_points' => $category['base_points'],
                ]
            );
        }
    }
}
