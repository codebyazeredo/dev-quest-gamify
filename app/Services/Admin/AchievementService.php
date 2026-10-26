<?php

namespace App\Services\Admin;

use App\Enums\AchievementConditionType;
use App\Enums\TitleUnlockType;
use App\Exceptions\DeletionBlockedException;
use App\Models\Achievement;
use App\Repositories\AchievementRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AchievementService
{
    public function __construct(
        private AchievementRepository $achievements,
        private TitleService $titles,
    ) {}

    /** @param  array{name: string, icon: ?string}|null  $rewardTitle  Título criado junto e concedido ao desbloquear a conquista. */
    public function create(array $data, ?array $rewardTitle = null): Achievement
    {
        return DB::transaction(function () use ($data, $rewardTitle) {
            $achievement = $this->achievements->create($this->mapAttributes($data));

            $this->createRewardTitle($achievement, $rewardTitle);

            return $achievement;
        });
    }

    public function update(Achievement $achievement, array $data, ?array $rewardTitle = null): Achievement
    {
        return DB::transaction(function () use ($achievement, $data, $rewardTitle) {
            $attributes = $this->mapAttributes($data);
            $attributes['active'] = $data['active'];

            $achievement = $this->achievements->update($achievement, $attributes);

            $this->createRewardTitle($achievement, $rewardTitle);

            return $achievement;
        });
    }

    public function toggleActive(Achievement $achievement): Achievement
    {
        return $this->achievements->update($achievement, ['active' => ! $achievement->active]);
    }

    public function duplicate(Achievement $achievement): Achievement
    {
        $name = $this->uniqueCopyName($achievement->name);

        return $this->achievements->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $achievement->description,
            'icon' => $achievement->icon,
            'condition_type' => $achievement->condition_type,
            'condition_value' => $achievement->condition_value,
            'xp_reward' => $achievement->xp_reward,
            'active' => false,
        ]);
    }

    public function delete(Achievement $achievement): void
    {
        if ($this->achievements->hasUnlocks($achievement)) {
            throw new DeletionBlockedException('Não é possível excluir uma conquista que usuários já desbloquearam.');
        }

        $this->achievements->delete($achievement);
    }

    private function createRewardTitle(Achievement $achievement, ?array $rewardTitle): void
    {
        if ($rewardTitle === null) {
            return;
        }

        $this->titles->create([
            'name' => $rewardTitle['name'],
            'icon' => $rewardTitle['icon'] ?? 'medal',
            'description' => "Concedido ao desbloquear a conquista \"{$achievement->name}\".",
            'unlock_type' => TitleUnlockType::ACHIEVEMENT->value,
            'achievement_id' => $achievement->id,
            'active' => true,
        ]);
    }

    private function uniqueCopyName(string $name): string
    {
        $candidate = "{$name} (cópia)";
        $suffix = 2;

        while ($this->achievements->nameExists($candidate)) {
            $candidate = "{$name} (cópia {$suffix})";
            $suffix++;
        }

        return $candidate;
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
