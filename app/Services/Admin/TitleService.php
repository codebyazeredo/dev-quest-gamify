<?php

namespace App\Services\Admin;

use App\Enums\TitleUnlockType;
use App\Exceptions\DeletionBlockedException;
use App\Models\Title;
use App\Repositories\AchievementRepository;
use App\Repositories\ChallengeRepository;
use App\Repositories\TitleRepository;
use App\Services\TitleService as GameTitleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TitleService
{
    public function __construct(
        private TitleRepository $titles,
        private AchievementRepository $achievements,
        private ChallengeRepository $challenges,
        private GameTitleService $gameTitles,
    ) {}

    public function paginate(int $perPage, string $search, string $unlockType, string $status): LengthAwarePaginator
    {
        return $this->titles->paginateFiltered($perPage, $search, $unlockType, $status);
    }

    /** @return array{achievements: Collection, challenges: Collection} */
    public function formOptions(): array
    {
        return ['achievements' => $this->achievements->all(), 'challenges' => $this->challenges->all()];
    }

    public function create(array $data): Title
    {
        $title = $this->titles->create($this->mapAttributes($data));

        $this->gameTitles->grantToEligibleUsers($title);

        return $title;
    }

    public function update(Title $title, array $data): Title
    {
        $title = $this->titles->update($title, $this->mapAttributes($data));

        $this->gameTitles->grantToEligibleUsers($title);

        return $title;
    }

    public function toggleActive(Title $title): Title
    {
        $title = $this->titles->update($title, ['active' => ! $title->active]);

        $this->gameTitles->grantToEligibleUsers($title);

        return $title;
    }

    public function delete(Title $title): void
    {
        if ($this->titles->hasUnlocks($title)) {
            throw new DeletionBlockedException('Usuários já desbloquearam este título.');
        }

        $this->titles->delete($title);
    }

    private function mapAttributes(array $data): array
    {
        $unlockType = TitleUnlockType::from($data['unlock_type'] ?? TitleUnlockType::ACHIEVEMENT->value);

        $attributes = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'icon' => $data['icon'] ?? null,
            'description' => $data['description'] ?? null,
            'unlock_type' => $unlockType,
            'achievement_id' => $unlockType === TitleUnlockType::ACHIEVEMENT ? ($data['achievement_id'] ?? null) : null,
            'challenge_id' => $unlockType === TitleUnlockType::CHALLENGE ? ($data['challenge_id'] ?? null) : null,
            'challenges_required' => $unlockType === TitleUnlockType::CHALLENGES_COUNT ? ($data['challenges_required'] ?? null) : null,
        ];

        // Sem o valor explícito o título nasce ativo; o modelo recém-criado precisa disso para ser concedido já.
        $attributes['active'] = $data['active'] ?? true;

        return $attributes;
    }
}
