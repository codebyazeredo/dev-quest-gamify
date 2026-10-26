<?php

namespace App\Services\Admin;

use App\Enums\ChallengeType;
use App\Enums\TitleUnlockType;
use App\Exceptions\DeletionBlockedException;
use App\Models\Challenge;
use App\Repositories\ChallengeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChallengeService
{
    public function __construct(
        private ChallengeRepository $challenges,
        private TitleService $titles,
    ) {}

    /** @param  array{name: string, icon: ?string}|null  $rewardTitle  Título criado junto e concedido ao concluir o desafio. */
    public function create(array $data, ?array $rewardTitle = null): Challenge
    {
        return DB::transaction(function () use ($data, $rewardTitle) {
            $challenge = $this->challenges->create($this->mapAttributes($data));

            $this->createRewardTitle($challenge, $rewardTitle);

            return $challenge;
        });
    }

    public function update(Challenge $challenge, array $data, ?array $rewardTitle = null): Challenge
    {
        return DB::transaction(function () use ($challenge, $data, $rewardTitle) {
            $attributes = $this->mapAttributes($data);
            $attributes['active'] = $data['active'];

            $challenge = $this->challenges->update($challenge, $attributes);

            $this->createRewardTitle($challenge, $rewardTitle);

            return $challenge;
        });
    }

    public function toggleActive(Challenge $challenge): Challenge
    {
        return $this->challenges->update($challenge, ['active' => ! $challenge->active]);
    }

    public function duplicate(Challenge $challenge): Challenge
    {
        $duration = (int) $challenge->starts_at->diffInSeconds($challenge->ends_at);
        $startsAt = $challenge->ends_at->addSecond();
        $name = $this->uniqueCopyName($challenge->name);

        return $this->challenges->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $challenge->description,
            'icon' => $challenge->icon,
            'type' => $challenge->type,
            'target' => $challenge->target,
            'xp_reward' => $challenge->xp_reward,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addSeconds($duration),
            'active' => false,
        ]);
    }

    public function delete(Challenge $challenge): void
    {
        if ($this->challenges->hasProgress($challenge)) {
            throw new DeletionBlockedException('Não é possível excluir um desafio no qual usuários já fizeram progresso.');
        }

        $this->challenges->delete($challenge);
    }

    private function createRewardTitle(Challenge $challenge, ?array $rewardTitle): void
    {
        if ($rewardTitle === null) {
            return;
        }

        $this->titles->create([
            'name' => $rewardTitle['name'],
            'icon' => $rewardTitle['icon'] ?? 'medal',
            'description' => "Concedido ao concluir o desafio \"{$challenge->name}\".",
            'unlock_type' => TitleUnlockType::CHALLENGE->value,
            'challenge_id' => $challenge->id,
            'active' => true,
        ]);
    }

    private function uniqueCopyName(string $name): string
    {
        $candidate = "{$name} (cópia)";
        $suffix = 2;

        while ($this->challenges->nameExists($candidate)) {
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
            'icon' => $data['icon'] ?? 'flag',
            'type' => ChallengeType::from($data['type']),
            'target' => $data['target'],
            'xp_reward' => $data['xp_reward'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
        ];
    }
}
