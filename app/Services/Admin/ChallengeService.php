<?php

namespace App\Services\Admin;

use App\Enums\ChallengeType;
use App\Exceptions\DeletionBlockedException;
use App\Models\Challenge;
use App\Repositories\ChallengeRepository;
use Illuminate\Support\Str;

class ChallengeService
{
    public function __construct(private ChallengeRepository $challenges) {}

    public function create(array $data): Challenge
    {
        return $this->challenges->create($this->mapAttributes($data));
    }

    public function update(Challenge $challenge, array $data): Challenge
    {
        $attributes = $this->mapAttributes($data);
        $attributes['active'] = $data['active'];

        return $this->challenges->update($challenge, $attributes);
    }

    public function delete(Challenge $challenge): void
    {
        if ($this->challenges->hasProgress($challenge)) {
            throw new DeletionBlockedException('Não é possível excluir um desafio no qual usuários já fizeram progresso.');
        }

        $this->challenges->delete($challenge);
    }

    private function mapAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'type' => ChallengeType::from($data['type']),
            'target' => $data['target'],
            'xp_reward' => $data['xp_reward'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
        ];
    }
}
