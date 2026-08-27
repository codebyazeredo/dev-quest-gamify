<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\TaskPriority;
use App\Repositories\TaskPriorityRepository;
use Illuminate\Support\Str;

class PriorityService
{
    public function __construct(private TaskPriorityRepository $priorities) {}

    public function create(array $data): TaskPriority
    {
        return $this->priorities->create($this->mapAttributes($data));
    }

    public function update(TaskPriority $priority, array $data): TaskPriority
    {
        return $this->priorities->update($priority, $this->mapAttributes($data));
    }

    public function delete(TaskPriority $priority): void
    {
        if ($this->priorities->hasTasks($priority)) {
            throw new DeletionBlockedException('Esta gravidade ainda possui tarefas.');
        }

        $this->priorities->delete($priority);
    }

    private function mapAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'multiplier' => $data['multiplier'],
        ];
    }
}
