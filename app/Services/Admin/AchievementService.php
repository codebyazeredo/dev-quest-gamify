<?php

namespace App\Services\Admin;

use App\Enums\AchievementConditionType;
use App\Exceptions\DeletionBlockedException;
use App\Models\Achievement;
use App\Repositories\AchievementRepository;
use Illuminate\Support\Str;

class AchievementService
{
    public function __construct(private AchievementRepository $achievements) {}

    public function create(array $data): Achievement
    {
        return $this->achievements->create($this->mapAttributes($data));
    }

    public function update(Achievement $achievement, array $data): Achievement
    {
        $attributes = $this->mapAttributes($data);
        $attributes['active'] = $data['active'];

        return $this->achievements->update($achievement, $attributes);
    }

    public function delete(Achievement $achievement): void
    {
        if ($this->achievements->hasUnlocks($achievement)) {
            throw new DeletionBlockedException('Não é possível excluir uma conquista que usuários já desbloquearam.');
        }

        $this->achievements->delete($achievement);
    }

    private function mapAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'icon' => $data['icon'],
            'condition_type' => AchievementConditionType::from($data['condition_type']),
            'condition_value' => $data['condition_value'],
            'xp_reward' => $data['xp_reward'],
        ];
    }
}
