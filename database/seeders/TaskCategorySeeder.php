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
            ['name' => 'Bug', 'base_points' => 10, 'color' => '#fee2e2', 'text_color' => '#7f1d1d'],
            ['name' => 'Feature', 'base_points' => 20, 'color' => '#dcfce7', 'text_color' => '#14532d'],
            ['name' => 'Refactoring', 'base_points' => 15, 'color' => '#ede9fe', 'text_color' => '#4c1d95'],
            ['name' => 'Improvement', 'base_points' => 15, 'color' => '#dbeafe', 'text_color' => '#1e3a8a'],
            ['name' => 'Infrastructure', 'base_points' => 20, 'color' => '#fef3c7', 'text_color' => '#78350f'],
            ['name' => 'Documentation', 'base_points' => 5, 'color' => '#e2e8f0', 'text_color' => '#1e293b'],
        ];

        foreach ($categories as $category) {
            TaskCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'base_points' => $category['base_points'],
                    'color' => $category['color'],
                    'text_color' => $category['text_color'],
                ]
            );
        }
    }
}
