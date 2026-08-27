<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\TaskCategory;
use App\Repositories\TaskCategoryRepository;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(private TaskCategoryRepository $categories) {}

    public function create(array $data): TaskCategory
    {
        return $this->categories->create($this->mapAttributes($data));
    }

    public function update(TaskCategory $category, array $data): TaskCategory
    {
        return $this->categories->update($category, $this->mapAttributes($data));
    }

    public function delete(TaskCategory $category): void
    {
        if ($this->categories->hasTasks($category)) {
            throw new DeletionBlockedException('Esta categoria ainda possui tarefas.');
        }

        $this->categories->delete($category);
    }

    private function mapAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'base_points' => $data['base_points'],
            'color' => $data['color'],
            'text_color' => $data['text_color'],
        ];
    }
}
