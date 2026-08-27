<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\Title;
use App\Repositories\TitleRepository;
use Illuminate\Support\Str;

class TitleService
{
    public function __construct(private TitleRepository $titles) {}

    public function create(array $data): Title
    {
        return $this->titles->create($this->mapAttributes($data));
    }

    public function update(Title $title, array $data): Title
    {
        return $this->titles->update($title, $this->mapAttributes($data));
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
        $attributes = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'icon' => $data['icon'],
            'achievement_id' => $data['achievement_id'],
        ];

        if (array_key_exists('active', $data)) {
            $attributes['active'] = $data['active'];
        }

        return $attributes;
    }
}
